@extends('vaultix::layout')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Scheduler Status -->
        <div class="p-6 rounded-2xl border bg-white shadow-sm flex items-center justify-between">
            <div>
                <h3 class="text-slate-500 text-sm font-medium">Scheduler Heartbeat</h3>
                <p class="text-2xl font-bold mt-1 {{ $isSchedulerHealthy ? 'text-slate-900' : 'text-rose-500' }}">
                    {{ $isSchedulerHealthy ? 'Running' : 'Offline' }}
                </p>
                <p class="text-xs text-slate-400 mt-1">Last run: {{ $schedulerLastRun ? $schedulerLastRun->diffForHumans() : 'Never' }}</p>
            </div>
            <div class="w-12 h-12 rounded-full {{ $isSchedulerHealthy ? 'bg-emerald-100 text-emerald-500' : 'bg-rose-100 text-rose-500' }} flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>

        <!-- Queue Status -->
        <div class="p-6 rounded-2xl border bg-white shadow-sm flex items-center justify-between">
            <div>
                <h3 class="text-slate-500 text-sm font-medium">Queue Worker</h3>
                <p class="text-2xl font-bold mt-1 {{ $isQueueHealthy ? 'text-slate-900' : 'text-amber-500' }}">
                    {{ $isQueueHealthy ? 'Active' : 'Not Detected' }}
                </p>
                <p class="text-xs text-slate-400 mt-1">Status check via system processes</p>
            </div>
            <div class="w-12 h-12 rounded-full {{ $isQueueHealthy ? 'bg-indigo-100 text-indigo-500' : 'bg-amber-100 text-amber-500' }} flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>
    </div>

    <!-- Backup Jobs Table -->
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="font-bold text-lg">Configured Backups</h2>
            <div class="flex gap-2">
                <a href="{{ route('vaultix.destinations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">Add Destination</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Job Name</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Storage</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Type</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Frequency</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Next Run</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($jobs as $job)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $job->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-medium uppercase">
                                {{ $job->destination->provider }}
                            </span>
                            <span class="text-xs text-slate-400 block mt-0.5">{{ $job->destination->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 uppercase">{{ $job->type }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ ucfirst($job->frequency) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $job->next_run_at ? \Carbon\Carbon::parse($job->next_run_at)->diffForHumans() : 'Pending' }}</td>
                        <td class="px-6 py-4 text-right flex justify-end gap-3">
                            <form action="{{ route('vaultix.destinations.test', $job->destination) }}" method="POST" class="inline">
                                @csrf
                                <button class="text-slate-400 hover:text-indigo-600 font-semibold text-xs flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                                    Test
                                </button>
                            </form>
                            <a href="{{ route('vaultix.destinations.edit', $job->destination) }}" class="text-slate-400 hover:text-amber-600 font-semibold text-xs flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit
                            </a>
                            <form action="{{ route('vaultix.run', $job) }}" method="POST" class="inline">
                                @csrf
                                <button class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Run Now</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($jobs->isEmpty())
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No backup jobs configured yet.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
