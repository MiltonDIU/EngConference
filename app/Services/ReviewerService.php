<?php

namespace App\Services;

use App\Models\Paper;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Session chairs who review the papers of their session.
 * Access is granted only through the paper_reviewers table, never through a permission,
 * so a reviewer sees nothing but the papers assigned to them.
 */
class ReviewerService
{
    public const ROLE_TITLE = 'Reviewer';

    // Columns of the "All Sessions" sheet, matched by header text
    private const HEADER_SESSION = 'Session';
    private const HEADER_EMAIL = 'Session Chair Email';
    private const HEADER_NAME = 'Session Chair';
    private const HEADER_DATE = 'Date';
    private const HEADER_MODE = 'Mode';
    private const HEADER_TIME = 'Time (BST)';
    private const HEADER_ABS_PREFIX = 'ABS ID ';

    public static function isReviewer(?User $user): bool
    {
        return $user !== null && DB::table('paper_reviewers')->where('user_id', $user->id)->exists();
    }

    public static function isAssigned(Paper $paper, ?User $user): bool
    {
        return $user !== null && DB::table('paper_reviewers')
            ->where('paper_id', $paper->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Admins (not participants) with paper_edit manage reviewer assignments.
     */
    public static function canManage(?User $user): bool
    {
        return $user !== null
            && !$user->roles->contains('id', 3)
            && \Illuminate\Support\Facades\Gate::forUser($user)->allows('paper_edit');
    }

    public static function roleId(): ?int
    {
        return Role::where('title', self::ROLE_TITLE)->value('id');
    }

    /**
     * Assigns one paper to a reviewer by hand. Returns false when it was already assigned.
     */
    public static function assign(User $user, Paper $paper, ?string $sessionInfo = null): bool
    {
        if (self::isAssigned($paper, $user)) {
            return false;
        }

        DB::transaction(function () use ($user, $paper, $sessionInfo) {
            // Keep the paper with the reviewer's existing session when none is given
            $existing = DB::table('paper_reviewers')->where('user_id', $user->id)->orderByDesc('id')->first();
            $user->reviewPapers()->attach($paper->id, [
                'session_no' => $sessionInfo ? null : $existing?->session_no,
                'session_info' => $sessionInfo ? mb_substr($sessionInfo, 0, 255) : $existing?->session_info,
                'position' => null,
            ]);
            if ($roleId = self::roleId()) {
                $user->roles()->syncWithoutDetaching([$roleId]);
            }
        });

        return true;
    }

    /**
     * Removes one paper from a reviewer; drops the Reviewer role when nothing is left.
     */
    public static function unassign(User $user, Paper $paper): bool
    {
        $removed = DB::table('paper_reviewers')->where('user_id', $user->id)->where('paper_id', $paper->id)->delete() > 0;

        if (!DB::table('paper_reviewers')->where('user_id', $user->id)->exists()) {
            self::removeReviewer($user->id);
        }

        return $removed;
    }

    /**
     * Removes every assignment of a reviewer and the Reviewer role. Returns the number of papers removed.
     */
    public static function removeReviewer(int $userId): int
    {
        $count = DB::table('paper_reviewers')->where('user_id', $userId)->delete();
        if ($roleId = self::roleId()) {
            DB::table('role_user')->where('user_id', $userId)->where('role_id', $roleId)->delete();
        }

        return $count;
    }

    /**
     * Reads the session-chair sheet and assigns each chair the papers of their session(s).
     * Existing users are reused (matched by email); missing ones are created as verified users.
     * Each listed chair's assignments are replaced with exactly what the sheet lists.
     *
     * @return array summary for the console
     */
    public static function importSessionChairs(string $path, string $sheetName = 'All Sessions', bool $dryRun = false): array
    {
        $sessions = self::readSessions($path, $sheetName);

        $summary = [
            'sessions' => count($sessions),
            'sessions_without_email' => [],
            'invalid_emails' => [],
            'users_created' => [],
            'users_existing' => 0,
            'trashed_users' => [],
            'assignments' => 0,
            'added' => 0,
            'removed' => 0,
            'reviewers_removed' => [],
            'missing_papers' => [],
        ];

        // Group sessions by chair email (one chair can run several sessions)
        $chairs = [];
        foreach ($sessions as $session) {
            $email = strtolower(trim($session['email']));
            if ($email === '') {
                $summary['sessions_without_email'][] = $session['session'] . ' (' . $session['info'] . ', ' . count($session['abs_ids']) . ' papers)';
                continue;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $summary['invalid_emails'][] = $email . ' (session ' . $session['session'] . ')';
                continue;
            }
            $chairs[$email]['name'] = $chairs[$email]['name'] ?? $session['name'];
            $chairs[$email]['sessions'][] = $session;
        }

        $allIds = collect($sessions)->pluck('abs_ids')->flatten()->unique()->values();
        $papers = Paper::whereIn('submission_id', $allIds)->pluck('id', 'submission_id');
        $summary['missing_papers'] = $allIds->diff($papers->keys())->values()->all();

        DB::beginTransaction();
        try {
            $roleId = self::roleId();
            if (!$roleId) {
                throw new \RuntimeException('Role "' . self::ROLE_TITLE . '" not found. Run the migrations first.');
            }

            $processed = [];
            foreach ($chairs as $email => $chair) {
                $user = User::withTrashed()->whereRaw('LOWER(email) = ?', [$email])->first();

                if ($user && $user->trashed()) {
                    $summary['trashed_users'][] = $email;
                    continue;
                }

                if (!$user) {
                    $user = User::create([
                        'name' => self::cleanName($chair['name']) ?: $email,
                        'email' => $email,
                        // Random password: the chair sets their own via "Forgot Password"
                        'password' => Str::random(40),
                    ]);
                    // Admin-created account, so no verification mail is needed
                    $user->forceFill(['email_verified_at' => now()->format(config('panel.date_format') . ' ' . config('panel.time_format'))])->save();
                    $summary['users_created'][] = $email;
                } else {
                    $summary['users_existing']++;
                }

                $user->roles()->syncWithoutDetaching([$roleId]);

                $assign = [];
                foreach ($chair['sessions'] as $session) {
                    foreach ($session['abs_ids'] as $position => $absId) {
                        if (!isset($papers[$absId])) {
                            continue;
                        }
                        $assign[$papers[$absId]] = [
                            'session_no' => is_numeric($session['session']) ? (int) $session['session'] : null,
                            'session_info' => mb_substr($session['info'], 0, 255),
                            'position' => $position + 1,
                        ];
                    }
                }

                $changes = $user->reviewPapers()->sync($assign);
                $summary['assignments'] += count($assign);
                $summary['added'] += count($changes['attached']);
                $summary['removed'] += count($changes['detached']);
                $processed[] = $user->id;
            }

            // Guard: a wrong or empty sheet must not wipe every assignment
            if (!$processed) {
                throw new \RuntimeException('No session chair with a valid email found in the sheet. Nothing was changed.');
            }

            // The sheet is the full list: chairs no longer in it lose their papers and the Reviewer role
            $staleIds = DB::table('paper_reviewers')->whereNotIn('user_id', $processed)->distinct()->pluck('user_id');
            foreach ($staleIds as $staleId) {
                $summary['removed'] += self::removeReviewer($staleId);
                $summary['reviewers_removed'][] = User::withTrashed()->whereKey($staleId)->value('email');
            }

            $dryRun ? DB::rollBack() : DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $summary;
    }

    /**
     * Rows of the sheet as [session, email, name, info, abs_ids[]].
     */
    public static function readSessions(string $path, string $sheetName): array
    {
        $rows = self::readSheet($path, $sheetName);

        // Header row = the one that has both "Session" and "Session Chair Email"
        $headerIndex = null;
        $columns = [];
        foreach ($rows as $index => $row) {
            $map = array_flip(array_map(fn ($v) => trim((string) $v), $row));
            if (isset($map[self::HEADER_SESSION], $map[self::HEADER_EMAIL])) {
                $headerIndex = $index;
                $columns = $map;
                break;
            }
        }
        if ($headerIndex === null) {
            throw new \RuntimeException('Header row with "' . self::HEADER_SESSION . '" and "' . self::HEADER_EMAIL . '" not found in sheet "' . $sheetName . '".');
        }

        $absColumns = [];
        foreach ($columns as $title => $col) {
            if (str_starts_with($title, self::HEADER_ABS_PREFIX)) {
                $absColumns[(int) substr($title, strlen(self::HEADER_ABS_PREFIX))] = $col;
            }
        }
        ksort($absColumns);

        $get = fn (array $row, string $header) => isset($columns[$header]) ? trim((string) ($row[$columns[$header]] ?? '')) : '';

        $sessions = [];
        foreach ($rows as $rowNumber => $row) {
            if ($rowNumber <= $headerIndex) {
                continue;
            }
            $session = $get($row, self::HEADER_SESSION);
            if ($session === '') {
                continue;
            }

            $absIds = [];
            foreach ($absColumns as $col) {
                $id = trim((string) ($row[$col] ?? ''));
                if ($id !== '') {
                    $absIds[] = $id;
                }
            }

            $sessions[] = [
                'session' => $session,
                'email' => $get($row, self::HEADER_EMAIL),
                'name' => $get($row, self::HEADER_NAME),
                'info' => implode(' · ', array_filter([$get($row, self::HEADER_DATE), $get($row, self::HEADER_MODE), $get($row, self::HEADER_TIME)])),
                'abs_ids' => $absIds,
            ];
        }

        return $sessions;
    }

    /**
     * Minimal .xlsx reader (zip + XML): rows of one sheet as [colIndex => value].
     */
    private static function readSheet(string $path, string $sheetName): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('Cannot open the spreadsheet: ' . $path);
        }

        try {
            $workbook = self::xml($zip, 'xl/workbook.xml');
            $rels = self::xml($zip, 'xl/_rels/workbook.xml.rels');

            $targets = [];
            foreach ($rels->Relationship as $rel) {
                $targets[(string) $rel['Id']] = (string) $rel['Target'];
            }

            $sheetPath = null;
            foreach ($workbook->sheets->sheet as $sheet) {
                if ((string) $sheet['name'] === $sheetName) {
                    $rid = (string) $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                    $target = $targets[$rid] ?? '';
                    $sheetPath = str_starts_with($target, '/') ? ltrim($target, '/') : 'xl/' . $target;
                }
            }
            if (!$sheetPath) {
                throw new \RuntimeException('Sheet "' . $sheetName . '" not found in the spreadsheet.');
            }

            $shared = [];
            if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                foreach (self::xml($zip, 'xl/sharedStrings.xml')->si as $si) {
                    $text = (string) $si->t;
                    foreach ($si->r as $run) {
                        $text .= (string) $run->t;
                    }
                    $shared[] = $text;
                }
            }

            $rows = [];
            foreach (self::xml($zip, $sheetPath)->sheetData->row as $row) {
                $cells = [];
                foreach ($row->c as $cell) {
                    $type = (string) $cell['t'];
                    if ($type === 'inlineStr') {
                        $value = (string) $cell->is->t;
                        foreach ($cell->is->r as $run) {
                            $value .= (string) $run->t;
                        }
                    } elseif ($type === 's') {
                        $value = $shared[(int) $cell->v] ?? '';
                    } else {
                        $value = (string) $cell->v;
                    }
                    $cells[self::columnIndex((string) $cell['r'])] = $value;
                }
                $rows[(int) $row['r']] = $cells;
            }

            return $rows;
        } finally {
            $zip->close();
        }
    }

    private static function xml(ZipArchive $zip, string $name): \SimpleXMLElement
    {
        $content = $zip->getFromName($name);
        if ($content === false) {
            throw new \RuntimeException('Spreadsheet part missing: ' . $name);
        }
        // Some writers prefix every element (x:row, x:c ...); drop it so plain names work
        $content = preg_replace('#<(/?)[A-Za-z0-9]+:(?=[A-Za-z])#', '<$1', $content);

        return simplexml_load_string($content);
    }

    private static function columnIndex(string $ref): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($ref));
        $index = 0;
        foreach (str_split($letters) as $letter) {
            $index = $index * 26 + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private static function cleanName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));

        return str_contains(strtolower($name), 'to be appointed') ? '' : $name;
    }
}
