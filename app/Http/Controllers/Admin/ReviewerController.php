<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paper;
use App\Models\User;
use App\Services\ReviewerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin: manage which session chair reviews which paper.
 */
class ReviewerController extends Controller
{
    public const DEFAULT_SHEET = 'public/documents/BNC2026_Session_Chairs_Emails_and_ABS_Submission_IDs_14Nov2026.xlsx';
    private const SESSION_SHEET_KEY = 'reviewer_sheet_path';

    public function index()
    {
        $this->authorizeManage();

        $reviewers = User::whereHas('reviewPapers')
            ->withCount('reviewPapers')
            ->with('roles')
            ->orderBy('name')
            ->get();

        // Session labels per reviewer, e.g. "Onsite 4 Dec 2026 #1"
        $sessionLabels = DB::table('paper_reviewers')
            ->select('user_id', 'session_no', 'session_info')
            ->distinct()
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->map(fn ($r) => trim(($r->session_no ? '#' . $r->session_no . ' ' : '') . ($r->session_info ?? '')))->filter()->values());

        // Reviewers who got their own paper (conflict of interest): user_id => [submission ids]
        $ownPapers = DB::table('paper_reviewers')
            ->join('papers', 'papers.id', '=', 'paper_reviewers.paper_id')
            ->whereColumn('papers.user_id', 'paper_reviewers.user_id')
            ->whereNull('papers.deleted_at')
            ->get(['paper_reviewers.user_id', 'papers.id', 'papers.submission_id'])
            ->groupBy('user_id');

        // Submission IDs per reviewer, in assignment order: user_id => [{id, submission_id}]
        $assignedPapers = DB::table('paper_reviewers')
            ->join('papers', 'papers.id', '=', 'paper_reviewers.paper_id')
            ->whereNull('papers.deleted_at')
            ->orderBy('paper_reviewers.id')
            ->get(['paper_reviewers.user_id', 'papers.id', 'papers.submission_id'])
            ->groupBy('user_id');

        // Reviewers with a conflict first, then by name
        $reviewers = $reviewers->sortBy(fn ($r) => [isset($ownPapers[$r->id]) ? 0 : 1, $r->name])->values();

        $unassignedCount = Paper::where('status', 'approved')->whereDoesntHave('reviewers')->count();
        $defaultSheetExists = is_file(base_path(self::DEFAULT_SHEET));
        $storedSheet = session(self::SESSION_SHEET_KEY);

        $importedOnlyCount = ReviewerService::importedOnlyReviewersQuery()->count();

        return view('admin.reviewers.index', compact('reviewers', 'sessionLabels', 'ownPapers', 'assignedPapers', 'unassignedCount', 'defaultSheetExists', 'storedSheet', 'importedOnlyCount'));
    }

    public function show(User $user)
    {
        $this->authorizeManage();

        $papers = $user->reviewPapers()
            ->with('user', 'files')
            ->orderBy('paper_reviewers.id')
            ->get();

        // Suggestions for the transfer form; any existing user's email is accepted
        $otherReviewers = User::whereHas('reviewPapers')
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.reviewers.show', compact('user', 'papers', 'otherReviewers'));
    }

    /**
     * Sync from the Excel sheet. dry_run=1 only previews; the previewed file is kept
     * in the session so "Confirm sync" applies exactly what was previewed.
     */
    public function sync(Request $request)
    {
        $this->authorizeManage();

        $request->validate([
            'sheet_file' => 'nullable|file|max:10240',
            'sheet_name' => 'nullable|string|max:100',
            'dry_run' => 'nullable|boolean',
            'use_stored' => 'nullable|boolean',
            'new_user_password' => 'nullable|string|min:8|max:100',
        ]);

        $dryRun = $request->boolean('dry_run');
        $sheetName = $request->input('sheet_name') ?: 'All Sessions';

        if ($request->hasFile('sheet_file')) {
            $upload = $request->file('sheet_file');
            if (strtolower($upload->getClientOriginalExtension()) !== 'xlsx') {
                return back()->with('error', 'Please upload an .xlsx file.');
            }
            $path = $upload->storeAs('reviewer_sheets', now()->format('Ymd_His') . '_' . Auth::id() . '.xlsx', 'local');
            $fullPath = storage_path('app/' . $path);
        } elseif ($request->boolean('use_stored') && session(self::SESSION_SHEET_KEY)) {
            $fullPath = session(self::SESSION_SHEET_KEY)['path'];
            $sheetName = session(self::SESSION_SHEET_KEY)['sheet'];
        } else {
            $fullPath = base_path(self::DEFAULT_SHEET);
        }

        if (!is_file($fullPath)) {
            return back()->with('error', 'Spreadsheet not found. Please upload the file again.');
        }

        try {
            $summary = ReviewerService::importSessionChairs($fullPath, $sheetName, $dryRun, $dryRun ? null : $request->input('new_user_password'));
        } catch (\Throwable $e) {
            Log::warning('Reviewer sync failed: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }

        $summary['dry_run'] = $dryRun;
        $summary['file'] = basename($fullPath);

        if ($dryRun) {
            session([self::SESSION_SHEET_KEY => ['path' => $fullPath, 'sheet' => $sheetName, 'name' => basename($fullPath)]]);
        } else {
            session()->forget(self::SESSION_SHEET_KEY);
        }

        return redirect()->route('admin.reviewers.index')
            ->with('sync_summary', $summary)
            ->with('message', $dryRun ? 'Preview ready. Nothing has been saved yet.' : 'Reviewers synced from the sheet.');
    }

    /**
     * One common password for every account created by the import (reviewer-only accounts).
     */
    public function setPassword(Request $request)
    {
        $this->authorizeManage();

        $data = $request->validate([
            'password' => 'required|string|min:8|max:100|confirmed',
        ]);

        $count = ReviewerService::setPasswordForImportedReviewers($data['password']);

        return redirect()->route('admin.reviewers.index')
            ->with('message', 'Common password set on ' . $count . ' reviewer accounts. Authors and admins were not changed.');
    }

    public function attach(Request $request, User $user)
    {
        $this->authorizeManage();

        $data = $request->validate([
            'submission_ids' => 'required|string|max:2000',
            'session_info' => 'nullable|string|max:255',
        ]);

        $ids = collect(preg_split('/[\s,;]+/', $data['submission_ids']))->filter()->unique()->values();
        $papers = Paper::whereIn('submission_id', $ids)->get()->keyBy('submission_id');

        $added = [];
        $already = [];
        foreach ($ids as $id) {
            if (!isset($papers[$id])) {
                continue;
            }
            ReviewerService::assign($user, $papers[$id], $data['session_info'] ?? null) ? $added[] = $id : $already[] = $id;
        }
        $notFound = $ids->diff($papers->keys())->values()->all();

        $redirect = redirect()->route('admin.reviewers.show', $user->id);
        if ($added) {
            $redirect->with('message', 'Assigned: ' . implode(', ', $added) . '.');
        }
        $problems = array_filter([
            $already ? 'Already assigned: ' . implode(', ', $already) : null,
            $notFound ? 'Submission ID not found: ' . implode(', ', $notFound) : null,
        ]);
        if ($problems) {
            $redirect->with('error', implode(' · ', $problems));
        }

        return $redirect;
    }

    /**
     * Hands this reviewer's papers (the ticked ones, or all) to another existing user.
     */
    public function transfer(Request $request, User $user)
    {
        $this->authorizeManage();

        $data = $request->validate([
            'to_email' => 'required|email|exists:users,email',
            'paper_ids' => 'nullable|array',
            'paper_ids.*' => 'integer',
        ], [
            'to_email.exists' => 'No user has this email. Create the user first from Users, then transfer.',
        ]);

        $to = User::where('email', $data['to_email'])->firstOrFail();
        if ($to->id === $user->id) {
            return back()->withInput()->with('error', 'Choose a different reviewer to transfer the papers to.');
        }

        $result = ReviewerService::transfer($user, $to, $data['paper_ids'] ?? null);
        if (!$result['moved'] && !$result['already']) {
            return back()->with('error', 'No papers were transferred.');
        }

        $message = collect([
            $result['moved'] ? 'Transferred to ' . $to->name . ': ' . implode(', ', $result['moved']) . '.' : null,
            $result['already'] ? $to->name . ' already had: ' . implode(', ', $result['already']) . ' (removed from ' . $user->name . ').' : null,
        ])->filter()->implode(' ');

        // Stay on this reviewer while they still have papers, otherwise open the new one
        return ReviewerService::isReviewer($user)
            ? redirect()->route('admin.reviewers.show', $user->id)->with('message', $message)
            : redirect()->route('admin.reviewers.show', $to->id)->with('message', $message . ' ' . $user->name . ' has no papers left and is no longer a reviewer.');
    }

    public function detach(User $user, Paper $paper)
    {
        $this->authorizeManage();

        ReviewerService::unassign($user, $paper);

        return ReviewerService::isReviewer($user)
            ? redirect()->route('admin.reviewers.show', $user->id)->with('message', $paper->submission_id . ' removed from ' . $user->name . '.')
            : redirect()->route('admin.reviewers.index')->with('message', $paper->submission_id . ' removed. ' . $user->name . ' has no papers left and is no longer a reviewer.');
    }

    public function destroy(User $user)
    {
        $this->authorizeManage();

        $count = ReviewerService::removeReviewer($user->id);

        return redirect()->route('admin.reviewers.index')
            ->with('message', $user->name . ' is no longer a reviewer (' . $count . ' papers removed).');
    }

    private function authorizeManage(): void
    {
        abort_unless(ReviewerService::canManage(Auth::user()), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }
}
