<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearSessionData extends Command
{
    protected $signature = 'session:clear';

    protected $description = 'Clear all session data';

    public function handle()
    {
        $sessionPath = storage_path('framework/sessions');
        File::cleanDirectory($sessionPath);

        $this->info('Session data cleared successfully.');
    }
}