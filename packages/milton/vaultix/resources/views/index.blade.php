@extends('vaultix::layout')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Disk Usage Status -->
        <div class="p-6 rounded-2xl border bg-white shadow-sm flex items-center justify-between {{ $diskUsage['is_low'] ? 'ring-2 ring-rose-500 bg-rose-50' : '' }}">
            <div class="flex-1">
                <h3 class="text-slate-500 text-sm font-medium">Server Disk Space</h3>
                <p class="text-xl font-bold mt-1 text-slate-900">{{ $diskUsage['free'] }} <span class="text-xs font-normal text-slate-400">Free</span></p>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="h-full {{ $diskUsage['is_low'] ? 'bg-rose-500' : 'bg-indigo-500' }}" style="width: {{ $diskUsage['percentage'] }}%"></div>
                </div>
            </div>
            <div class="ml-4 w-10 h-10 rounded-full {{ $diskUsage['is_low'] ? 'bg-rose-100 text-rose-500' : 'bg-indigo-100 text-indigo-500' }} flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>

        <!-- Backup Payload -->
        <div class="p-6 rounded-2xl border bg-white shadow-sm flex items-center justify-between">
            <div class="flex-1">
                <h3 class="text-slate-500 text-sm font-medium">Base Project Size</h3>
                <p class="text-xl font-bold mt-1 text-slate-900">{{ $projectSize['formatted'] }}</p>
                <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-wider">DB: {{ round($projectSize['db']/1024/1024, 1) }}MB | Files: {{ round($projectSize['files']/1024/1024, 1) }}MB</p>
            </div>
            <div class="ml-4 w-10 h-10 rounded-full bg-emerald-100 text-emerald-500 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
            </div>
        </div>

        <!-- Scheduler Status -->
        <div class="p-6 rounded-2xl border bg-white shadow-sm flex items-center justify-between">
            <div>
                <h3 class="text-slate-500 text-sm font-medium">Scheduler</h3>
                <p class="text-xl font-bold mt-1 {{ $isSchedulerHealthy ? 'text-slate-900' : 'text-rose-500' }}">
                    {{ $isSchedulerHealthy ? 'Healthy' : 'Offline' }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider">Last Run: {{ $schedulerLastRun ? $schedulerLastRun->diffForHumans() : 'Never' }}</p>
            </div>
            <div class="ml-4 w-10 h-10 rounded-full {{ $isSchedulerHealthy ? 'bg-emerald-100 text-emerald-500' : 'bg-rose-100 text-rose-500' }} flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Queue Status -->
        <div class="p-6 rounded-2xl border bg-white shadow-sm flex items-center justify-between">
            <div>
                <h3 class="text-slate-500 text-sm font-medium">Queue Worker</h3>
                <p class="text-xl font-bold mt-1 {{ $isQueueHealthy ? 'text-slate-900' : 'text-amber-500' }}">
                    {{ $isQueueHealthy ? 'Active' : 'Offline' }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider">Ready for jobs</p>
            </div>
            <div class="ml-4 w-10 h-10 rounded-full {{ $isQueueHealthy ? 'bg-indigo-100 text-indigo-500' : 'bg-amber-100 text-amber-500' }} flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>
    </div>

    @if($diskUsage['is_low'])
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-center gap-3 text-rose-700 animate-pulse">
        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div class="text-sm">
            <p class="font-bold">CRITICAL: Low Disk Space Detected!</p>
            <p>Server has only <b>{{ $diskUsage['free'] }}</b> left. Backup jobs might fail or crash the server. Please clear some space immediately.</p>
        </div>
    </div>
    @endif

    <!-- Backup Jobs Table -->
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="font-bold text-lg">Configured Backups</h2>
            <div class="flex gap-2">
                <a href="{{ route('vaultix.export') }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export
                </a>
                <form id="importForm" action="{{ route('vaultix.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="config_file" id="configFile" onchange="document.getElementById('importForm').submit()">
                </form>
                <button onclick="document.getElementById('configFile').click()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import
                </button>
                <a href="{{ route('vaultix.settings') }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Settings
                </a>
                <a href="{{ route('vaultix.destinations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">Add Storage</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Job Name</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Provider</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Retention / Capacity</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Next Run</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($jobs as $job)
                    @php
                        $backupsPerDay = ($job->frequency === 'hourly') ? 24 : (($job->frequency === '6_hours') ? 4 : (($job->frequency === '12_hours') ? 2 : 1));
                        if($job->frequency === 'weekly') $backupsPerDay = 1/7;
                        if($job->frequency === 'monthly') $backupsPerDay = 1/30;

                        $estFiles = 0;
                        if($job->frequency === 'monthly') { $estFiles = $job->keep_monthly_backups_for_months ?: 1; }
                        else if($job->frequency === 'weekly') { $estFiles = max($job->keep_weekly_backups_for_weeks, $job->keep_monthly_backups_for_months); }
                        else {
                            $estFiles = ($job->keep_all_backups_for_days * $backupsPerDay) + 
                                       max(0, $job->keep_daily_backups_for_days - $job->keep_all_backups_for_days) + 
                                       $job->keep_weekly_backups_for_weeks + $job->keep_monthly_backups_for_months;
                        }
                        
                        $estTotalStorage = $estFiles * ($projectSize['total'] * 0.4);
                        $formattedProjection = round($estTotalStorage / 1024 / 1024 / 1024, 2) . ' GB';
                        if($estTotalStorage < 1024*1024*1024) $formattedProjection = round($estTotalStorage / 1024 / 1024, 0) . ' MB';
                    @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $job->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-medium uppercase">
                                {{ $job->destination->provider }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-slate-700">~{{ ceil($estFiles) }} Files</span>
                                <span class="text-xs text-slate-400">/</span>
                                <span class="text-xs font-semibold text-indigo-600">Est. {{ $formattedProjection }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $job->next_run_at ? \Carbon\Carbon::parse($job->next_run_at)->diffForHumans() : 'Pending' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <form action="{{ route('vaultix.destinations.test', $job->destination) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Test Connection" class="p-2 bg-slate-50 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-all border border-transparent hover:border-indigo-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                                    </button>
                                </form>
                                <a href="{{ route('vaultix.destinations.edit', $job->destination) }}" title="Edit Job" class="p-2 bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 rounded-lg transition-all border border-transparent hover:border-amber-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form onsubmit="handleRunNow(event, this, '{{ route('vaultix.run', $job) }}')" class="inline">
                                    @csrf
                                    <button type="submit" title="Run Backup Now" class="run-now-btn px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all text-xs font-bold shadow-sm">
                                        Run Now
                                    </button>
                                </form>
                                <form action="{{ route('vaultix.destinations.destroy', $job->destination) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this job and destination?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Delete Job" class="p-2 bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 rounded-lg transition-all border border-transparent hover:border-rose-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Backup History Table -->
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="font-bold text-lg">Recent Backup History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Date & Time</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Destination</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">File Name</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Size</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($backups as $backup)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <span class="text-slate-900 font-medium block">{{ $backup->completed_at ? $backup->completed_at->format('M d, Y') : 'N/A' }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ $backup->completed_at ? $backup->completed_at->format('H:i:s') : '' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600 block">{{ $backup->job->name ?? 'Unknown' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 truncate max-w-[150px]">
                            {{ $backup->file_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $backup->human_size }}</td>
                        <td class="px-6 py-4">
                            @if($backup->status === 'success')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase">Success</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 uppercase">Failed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                @if($backup->status === 'success')
                                    <a href="{{ route('vaultix.backups.download', $backup) }}" onclick="handleDownloadClick(this)" title="Download Backup" class="download-btn p-2 bg-slate-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg transition-all border border-transparent hover:border-indigo-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                @endif
                                <form action="{{ route('vaultix.backups.destroy', $backup) }}" method="POST" onsubmit="return handleSafeDelete(this)" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Delete Permanent" class="delete-btn p-2 bg-slate-50 text-slate-400 hover:bg-rose-600 hover:text-white rounded-lg transition-all border border-transparent hover:border-rose-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let currentLatestId = {{ $backups->first()?->id ?? 0 }};
    let pollingInterval = null;

    function startPollingForBackups() {
        if (pollingInterval) return;
        pollingInterval = setInterval(() => {
            fetch('{{ route('vaultix.backups.latest-id') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.id > currentLatestId) {
                        clearInterval(pollingInterval);
                        window.location.reload();
                    }
                })
                .catch(err => console.error('Polling error:', err));
        }, 5000);
    }

    function handleRunNow(event, form, url) {
        event.preventDefault();
        const btn = form.querySelector('.run-now-btn');
        const originalText = btn.innerText;
        
        btn.innerText = 'Checking...';
        btn.classList.add('opacity-50', 'pointer-events-none');

        fetch(url, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json();
            
            if (!response.ok) {
                // Show specific error message (e.g., Insufficient Storage)
                alert(data.error || 'Failed to start backup.');
                btn.innerText = originalText;
                btn.classList.remove('opacity-50', 'pointer-events-none');
                return;
            }

            // Success: dispatched
            btn.innerText = 'Backing up...';
            startPollingForBackups();
        })
        .catch(err => {
            console.error('Run Now failed:', err);
            btn.innerText = originalText;
            btn.classList.remove('opacity-50', 'pointer-events-none');
            alert('A technical error occurred.');
        });
    }

    function handleDownloadClick(btn) {
        const originalText = btn.innerText;
        btn.innerText = 'Preparing...';
        btn.classList.add('opacity-50', 'pointer-events-none');
        const checkCookie = setInterval(() => {
            if (document.cookie.split(';').some((item) => item.trim().startsWith('vaultix_download_started='))) {
                btn.innerText = originalText;
                btn.classList.remove('opacity-50', 'pointer-events-none');
                clearInterval(checkCookie);
            }
        }, 500);
    }

    function handleSafeDelete(form) {
        if (!confirm('Are you sure?')) return false;
        const btn = form.querySelector('.delete-btn');
        btn.innerText = 'Deleting...';
        btn.classList.add('opacity-50', 'pointer-events-none');
        return true;
    }
</script>
@endsection
