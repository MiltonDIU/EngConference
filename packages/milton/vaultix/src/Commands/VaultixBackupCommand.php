<?php

namespace Milton\Vaultix\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Milton\Vaultix\Models\BackupJob;

class VaultixBackupCommand extends Command
{
    protected $signature = 'vaultix:run {--job= : ID of a specific job to run immediately}';
    protected $description = 'Run scheduled dynamic backups managed by Vaultix';

    public function handle()
    {
        $specificJobId = $this->option('job');

        if ($specificJobId) {
            $jobs = BackupJob::where('id', $specificJobId)->get();
            $this->info("Manual trigger for Job ID: {$specificJobId}");
        } else {
            $this->info("System time: " . now()->toDateTimeString());

            $jobs = BackupJob::where('is_enabled', true)
                ->where(function ($query) {
                    $query->whereNull('next_run_at')
                          ->orWhere('next_run_at', '<=', now());
                })->get();

            $this->info("Total jobs found in DB: " . BackupJob::count());
            $this->info("Pending jobs matching criteria: " . $jobs->count());
        }

        if ($jobs->isEmpty()) {
            $this->info('No pending backup jobs found.');
            
            // Debug: Show why it didn't match
            if ($first = BackupJob::first()) {
                $this->info("First job Next Run: " . $first->next_run_at);
                $this->info("Is Enabled: " . $first->is_enabled);
            }
            return;
        }

        foreach ($jobs as $job) {
            $this->processJob($job);
        }

        Cache::put('vaultix_scheduler_heartbeat', now(), now()->addDays(2));
    }

    protected function processJob($job)
    {
        $this->info("Starting Job: {$job->name}");

        $dest = $job->destination;
        $creds = $dest->credentials;

        // 1. Determine backup folder name
        $rawFolderName = !empty($job->custom_folder_name) ? $job->custom_folder_name : config('app.name', 'VaultixBackup');
        $cleanName = preg_replace('/[^A-Za-z0-9 ]/', '', $rawFolderName);
        $folderName = str_replace(' ', '', ucwords($cleanName));

        // 2. Register the storage disk at runtime
        $diskConfig = $this->getDiskConfig($dest);
        
        // Force Laravel to see this disk
        \Illuminate\Support\Facades\Config::set('filesystems.disks.vaultix_disk', $diskConfig);
        $this->info("Using Root Folder ID: " . ($diskConfig['folderId'] ?? 'None'));
        $this->info("Project Subfolder: " . $folderName);
        $this->info("Backup Name (Config): " . config('backup.backup.name'));

        // 3. Override backup config explicitly
        \Illuminate\Support\Facades\Config::set('backup.backup.name', $folderName);
        \Illuminate\Support\Facades\Config::set('backup.backup.destination.disks', ['vaultix_disk']);
        
        // Exclude specific folders always
        \Illuminate\Support\Facades\Config::set('backup.backup.source.files.exclude', [
            base_path('vendor'),
            base_path('node_modules'),
            storage_path('app/backup-temp'),
        ]);

        // 4. Configure Notifications
        $notificationEmail = $job->notification_email ?? config('mail.from.address');
        \Illuminate\Support\Facades\Config::set('backup.notifications.mail.to', $notificationEmail);
        
        // Force Spatie to use mail for these specific notifications
        \Illuminate\Support\Facades\Config::set('backup.notifications.notifications', [
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => ($job->notify_on_success ? ['mail'] : []),
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ($job->notify_on_failure ? ['mail'] : []),
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => [],
        ]);

        $this->info("Verifying config: vaultix_disk (Folder: {$folderName})");

        // 5. Run the Spatie Backup Engine
        try {
            $this->info("Starting backup:run for {$job->name}...");
            
            // Pre-create the directory to prevent Flysystem from throwing an UnableToListContents
            // exception when Spatie Backup checks if the disk is reachable.
            \Illuminate\Support\Facades\Storage::disk('vaultix_disk')->makeDirectory($folderName);

            $params = [
                '--only-to-disk' => 'vaultix_disk',
                '--destination-path' => $folderName,
                '--exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    storage_path('app/backup-temp'),
                ],
                '--no-interaction' => true,
            ];

            if ($job->type === 'db_only') {
                $params['--only-db'] = true;
            } elseif ($job->type === 'files_only') {
                $params['--only-files'] = true;
            }

            Artisan::call('backup:run', $params);
            
            $output = Artisan::output();
            $this->info($output);
            \Illuminate\Support\Facades\Log::info("Vaultix Backup Success for {$job->name}: " . $output);
        } catch (\Exception $e) {
            $errorMsg = "Backup failed for {$job->name}: " . $e->getMessage();
            $this->error($errorMsg);
            \Illuminate\Support\Facades\Log::error($errorMsg);
        }

        $job->update([
            'last_run_at' => now(),
            'next_run_at' => $this->calculateNextRun($job->frequency)
        ]);
        
        $this->info("Job '{$job->name}' processed.");
    }

    protected function getDiskConfig($dest)
    {
        $creds = $dest->credentials;
        if ($dest->provider === 'gdrive') {
            $folderId = $creds['folder_id'] ?? null;
            return [
                'driver'       => 'google',
                'clientId'     => $creds['client_id'] ?? null,
                'clientSecret' => $creds['client_secret'] ?? null,
                'refreshToken' => $creds['refresh_token'] ?? null,
                'folderId'     => $folderId, // This is the PARENT folder only
                // DO NOT set root/path here — Spatie uses backup.backup.name as the subfolder
            ];
        }

        if ($dest->provider === 'sftp') {
            return [
                'driver'   => 'sftp',
                'host'     => $creds['host'] ?? null,
                'username' => $creds['username'] ?? null,
                'password' => $creds['password'] ?? null,
                'port'     => (int) ($creds['port'] ?? 22),
                'root'     => $creds['root'] ?? '/',
                // Spatie uses backup.backup.name as the subfolder inside root
                'timeout'  => 30,
            ];
        }

        if ($dest->provider === 's3' || $dest->provider === 'r2') {
            $config = [
                'driver'                  => 's3',
                'key'                     => $creds['key'] ?? ($creds['access_key'] ?? ($creds['r2_key'] ?? null)),
                'secret'                  => $creds['secret'] ?? ($creds['secret_key'] ?? ($creds['r2_secret'] ?? null)),
                'region'                  => $dest->provider === 'r2' ? 'auto' : ($creds['region'] ?? 'us-east-1'),
                'bucket'                  => $creds['bucket'] ?? ($creds['r2_bucket'] ?? null),
                'endpoint'                => $creds['endpoint'] ?? null,
                'use_path_style_endpoint' => $dest->provider === 'r2' || ($creds['use_path_style'] ?? false),
                'url'                     => $creds['url'] ?? null,
                'visibility'              => $creds['visibility'] ?? null,
            ];

            return $config;
        }

        return [];
    }

    protected function calculateNextRun($frequency)
    {
        return match($frequency) {
            'hourly' => now()->addHour(),
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
        };
    }
}
