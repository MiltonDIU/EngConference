<?php

namespace App\Console\Commands;

use App\Services\ReviewerService;
use Illuminate\Console\Command;

class SetImportedReviewerPassword extends Command
{
    protected $signature = 'reviewers:set-password
                            {--password= : The common password (asked for if omitted)}
                            {--dry-run : Only list the accounts that would change}';

    protected $description = 'Set one common password on reviewer accounts created by the session-chair import (accounts whose only role is Reviewer)';

    public function handle(): int
    {
        $users = ReviewerService::importedOnlyReviewersQuery()->orderBy('email')->pluck('email');

        $this->info($users->count() . ' reviewer-only accounts (authors and admins are not touched).');
        if ($this->output->isVerbose() || $this->option('dry-run')) {
            $users->each(fn ($email) => $this->line('  ' . $email));
        }
        if ($this->option('dry-run') || $users->isEmpty()) {
            return self::SUCCESS;
        }

        $password = $this->option('password') ?: $this->secret('Common password');
        if (mb_strlen((string) $password) < 8) {
            $this->error('The password must be at least 8 characters.');
            return self::FAILURE;
        }

        if (!$this->option('no-interaction') && !$this->confirm('Set this password on ' . $users->count() . ' accounts?', true)) {
            $this->warn('Cancelled.');
            return self::SUCCESS;
        }

        $count = ReviewerService::setPasswordForImportedReviewers($password);
        $this->info('Password set on ' . $count . ' accounts.');

        return self::SUCCESS;
    }
}
