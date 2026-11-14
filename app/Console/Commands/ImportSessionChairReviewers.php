<?php

namespace App\Console\Commands;

use App\Services\ReviewerService;
use Illuminate\Console\Command;

class ImportSessionChairReviewers extends Command
{
    protected $signature = 'reviewers:import-session-chairs
                            {file=public/documents/BNC2026_Session_Chairs_Emails_and_ABS_Submission_IDs_14Nov2026.xlsx : Path to the .xlsx (relative to the project root)}
                            {--sheet=All Sessions : Sheet that lists one session per row}
                            {--dry-run : Show what would happen without saving anything}';

    protected $description = 'Add session chairs as reviewers and assign them the papers (ABS IDs) of their sessions';

    public function handle(): int
    {
        $file = $this->argument('file');
        $path = str_starts_with($file, '/') ? $file : base_path($file);

        if (!is_file($path)) {
            $this->error('File not found: ' . $path);
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN: nothing will be saved.');
        }

        try {
            $s = ReviewerService::importSessionChairs($path, $this->option('sheet'), $dryRun);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->table(['Item', 'Count'], [
            ['Sessions in sheet', $s['sessions']],
            ['Reviewers: existing users', $s['users_existing']],
            ['Reviewers: new users created', count($s['users_created'])],
            ['Papers assigned (total)', $s['assignments']],
            ['  newly added', $s['added']],
            ['  removed', $s['removed']],
            ['Reviewers removed (not in sheet any more)', count($s['reviewers_removed'])],
            ['Sessions skipped (no chair email)', count($s['sessions_without_email'])],
            ['ABS IDs not found in papers', count($s['missing_papers'])],
        ]);

        if ($s['sessions_without_email']) {
            $this->warn('Sessions without chair email (papers not assigned):');
            foreach ($s['sessions_without_email'] as $session) {
                $this->line('  - Session ' . $session);
            }
        }
        if ($s['invalid_emails']) {
            $this->warn('Invalid emails skipped: ' . implode(', ', $s['invalid_emails']));
        }
        if ($s['trashed_users']) {
            $this->warn('Deleted (trashed) user accounts skipped: ' . implode(', ', $s['trashed_users']));
        }
        if ($s['reviewers_removed']) {
            $this->warn('No longer reviewers: ' . implode(', ', $s['reviewers_removed']));
        }
        if ($s['missing_papers']) {
            $this->warn('ABS IDs not found: ' . implode(', ', $s['missing_papers']));
        }
        if ($s['users_created'] && $this->output->isVerbose()) {
            $this->line('New users: ' . implode(', ', $s['users_created']));
        }

        $this->info($dryRun ? 'Dry run finished.' : 'Reviewers imported.');

        return self::SUCCESS;
    }
}
