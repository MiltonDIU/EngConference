<?php

namespace Milton\Vaultix\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Milton\Vaultix\Models\BackupDestination;
use Milton\Vaultix\Models\BackupJob;

class VaultixController extends Controller
{
    public function index()
    {
        $destinations = BackupDestination::all();
        $jobs = BackupJob::with('destination')->get();
        
        $schedulerLastRun = Cache::get('vaultix_scheduler_heartbeat');
        $isSchedulerHealthy = $schedulerLastRun && $schedulerLastRun->diffInMinutes(now()) < 65;
        
        $isQueueHealthy = false;
        if (function_exists('exec')) {
            exec("ps aux | grep 'queue:work' | grep -v grep", $output);
            $isQueueHealthy = count($output) > 0;
        }

        return view('vaultix::index', compact('destinations', 'jobs', 'isSchedulerHealthy', 'isQueueHealthy', 'schedulerLastRun'));
    }

    public function createDestination()
    {
        return view('vaultix::destinations.create');
    }

    public function storeDestination(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|in:gdrive,s3,r2,sftp',
            'credentials' => 'required|array',
            'backup_type' => 'required|in:db_only,files_only,full',
            'frequency' => 'required|in:daily,weekly,monthly,hourly',
            'custom_folder_name' => 'nullable|string|max:255',
            'notification_email' => 'nullable|email|max:255',
        ]);

        $destination = BackupDestination::create($request->only(['name', 'provider', 'credentials']));

        // Create the backup job with user selections
        BackupJob::create([
            'destination_id' => $destination->id,
            'name' => $destination->name . " (" . ucfirst($request->frequency) . ")",
            'type' => $request->backup_type,
            'custom_folder_name' => $request->custom_folder_name,
            'notification_email' => $request->notification_email,
            'notify_on_success' => $request->has('notify_on_success') ? 1 : 0,
            'notify_on_failure' => $request->has('notify_on_failure') ? 1 : 0,
            'frequency' => $request->frequency,
            'next_run_at' => now()->addMinutes(5), // Run soon for first time
        ]);

        return redirect()->route('vaultix.index')->with('success', 'Storage destination added and default job created!');
    }

    public function editDestination(BackupDestination $destination)
    {
        $job = $destination->jobs()->first();
        return view('vaultix::destinations.edit', compact('destination', 'job'));
    }

    public function updateDestination(Request $request, BackupDestination $destination)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|in:gdrive,s3,r2,sftp',
            'credentials' => 'required|array',
            'backup_type' => 'required|in:db_only,files_only,full',
            'frequency' => 'required|in:daily,weekly,monthly,hourly',
            'custom_folder_name' => 'nullable|string|max:255',
            'notification_email' => 'nullable|email|max:255',
        ]);

        $destination->update($request->only(['name', 'provider', 'credentials']));

        // Update the associated job
        $job = $destination->jobs()->first();
        if ($job) {
            $job->update([
                'name' => $destination->name . " (" . ucfirst($request->frequency) . ")",
                'type' => $request->backup_type,
                'custom_folder_name' => $request->custom_folder_name,
                'notification_email' => $request->notification_email,
                'notify_on_success' => $request->has('notify_on_success') ? 1 : 0,
                'notify_on_failure' => $request->has('notify_on_failure') ? 1 : 0,
                'frequency' => $request->frequency,
            ]);
        }

        return redirect()->route('vaultix.index')->with('success', 'Backup configuration updated successfully!');
    }

    public function destroyDestination(BackupDestination $destination)
    {
        $destination->delete();
        return redirect()->route('vaultix.index')->with('success', 'Destination and associated jobs deleted.');
    }

    public function testConnection(BackupDestination $destination)
    {
        try {
            // 1. Setup a temporary disk configuration
            $command = new \Milton\Vaultix\Commands\VaultixBackupCommand();
            $diskConfig = $this->getDiskConfigForTesting($destination);
            
            config(['filesystems.disks.vaultix_test' => $diskConfig]);
            
            // 2. Try to list files or check existence
            $exists = \Illuminate\Support\Facades\Storage::disk('vaultix_test')->allFiles('/');
            
            return back()->with('success', "Connection successful! Found files in storage.");
        } catch (\Exception $e) {
            return back()->with('error', "Connection failed: " . $e->getMessage());
        }
    }

    protected function getDiskConfigForTesting($dest)
    {
        $creds = $dest->credentials;
        if ($dest->provider === 'gdrive') {
            return [
                'driver' => 'google',
                'clientId' => $creds['client_id'] ?? null,
                'clientSecret' => $creds['client_secret'] ?? null,
                'refreshToken' => $creds['refresh_token'] ?? null,
                'folderId' => $creds['folder_id'] ?? null,
            ];
        }

        if ($dest->provider === 'sftp') {
            return [
                'driver' => 'sftp',
                'host' => $creds['host'] ?? null,
                'username' => $creds['username'] ?? null,
                'password' => $creds['password'] ?? null,
                'port' => $creds['port'] ?? 22,
                'root' => $creds['root'] ?? '/',
            ];
        }

        return [
            'driver' => 's3',
            'key' => $creds['key'] ?? ($creds['r2_key'] ?? null),
            'secret' => $creds['secret'] ?? ($creds['r2_secret'] ?? null),
            'region' => $creds['region'] ?? 'us-east-1',
            'bucket' => $creds['bucket'] ?? ($creds['r2_bucket'] ?? null),
            'endpoint' => $dest->provider === 'r2' ? ($creds['endpoint'] ?? null) : null,
            'use_path_style_endpoint' => $dest->provider === 'r2' ? true : false,
        ];
    }

    public function redirectToGoogle(Request $request)
    {
        $clientId = $request->query('client_id');
        $clientSecret = $request->query('client_secret');

        if (!$clientId || !$clientSecret) {
            return back()->with('error', 'Please enter Client ID and Secret first.');
        }

        session(['vaultix_gdrive_creds' => ['id' => $clientId, 'secret' => $clientSecret]]);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => route('vaultix.auth.google.callback'),
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/drive.readonly',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->query('code');
        $creds = session('vaultix_gdrive_creds');

        if (!$code || !$creds) {
            return redirect()->route('vaultix.destinations.create')->with('error', 'Authorization failed or timed out.');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $creds['id'],
                'client_secret' => $creds['secret'],
                'redirect_uri' => route('vaultix.auth.google.callback'),
                'grant_type' => 'authorization_code',
            ]);

            $data = $response->json();

            if (isset($data['refresh_token'])) {
                return redirect()->route('vaultix.destinations.create', [
                    'refresh_token' => $data['refresh_token'],
                    'client_id' => $creds['id'],
                    'client_secret' => $creds['secret'],
                    'provider' => 'gdrive'
                ])->with('success', 'Google Drive authorized! Refresh token generated.');
            }

            return redirect()->route('vaultix.destinations.create')->with('error', 'Failed to get refresh token. Make sure you chose "Consent" during login.');
        } catch (\Exception $e) {
            return redirect()->route('vaultix.destinations.create')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function runNow(BackupJob $job)
    {
        \Milton\Vaultix\Jobs\ProcessVaultixBackup::dispatch($job->id);
        return back()->with('success', 'Backup job has been dispatched to the background queue!');
    }
}
