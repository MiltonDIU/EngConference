@extends('vaultix::layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Configure New Backup</h1>
        <p class="text-slate-500">Set up your storage destination and backup schedule.</p>
    </div>

    <form action="{{ route('vaultix.destinations.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
            <div class="p-8 space-y-8">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Destination Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Daily Cloud Storage" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" required>
                </div>

                <!-- Provider Selection -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-3">1. Select Storage Provider</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="provider-options">
                        @foreach(['gdrive' => 'Google Drive', 's3' => 'AWS S3', 'r2' => 'Cloudflare R2', 'sftp' => 'SFTP / Custom'] as $val => $label)
                        <label class="provider-label relative flex flex-col items-center p-4 border-2 rounded-2xl cursor-pointer hover:border-indigo-200 transition-all duration-200">
                            <input type="radio" name="provider" value="{{ $val }}" class="absolute opacity-0" {{ $val == 'gdrive' ? 'checked' : '' }} onclick="updateSelection(this, 'provider-label')">
                            <span class="text-sm font-bold text-slate-600">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Backup Type -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-3">2. What to backup?</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach(['full' => 'Database & Files', 'db_only' => 'Database Only', 'files_only' => 'Files Only'] as $val => $label)
                        <label class="type-label flex items-center p-3 border-2 rounded-xl cursor-pointer hover:border-indigo-100 transition {{ old('backup_type', 'full') == $val ? 'border-indigo-500 bg-indigo-50' : '' }}">
                            <input type="radio" name="backup_type" value="{{ $val }}" class="mr-3" {{ old('backup_type', 'full') == $val ? 'checked' : '' }} onclick="updateSelection(this, 'type-label')">
                            <span class="text-sm font-medium">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Frequency -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-3">3. Backup Frequency</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'hourly' => 'Hourly'] as $val => $label)
                        <label class="freq-label flex items-center p-3 border-2 rounded-xl cursor-pointer hover:border-indigo-100 transition">
                            <input type="radio" name="frequency" value="{{ $val }}" class="mr-3" {{ old('frequency', 'daily') == $val ? 'checked' : '' }} onclick="updateSelection(this, 'freq-label')">
                            <span class="text-sm font-medium">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Dynamic Credentials -->
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                    <div id="fields-gdrive" class="provider-fields space-y-4">
                        <!-- Google Drive Setup Guide -->
                        <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-xl space-y-4 mb-6">
                            <div class="flex items-center gap-2 text-indigo-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="font-bold uppercase tracking-wider text-sm">Google Drive Detailed Setup Guide</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-indigo-600 leading-relaxed">
                                <div class="space-y-2">
                                    <p class="font-bold text-indigo-800">1. Google Cloud Console</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Go to <a href="https://console.cloud.google.com/" target="_blank" class="underline font-bold">Cloud Console</a> and create a project.</li>
                                        <li>Enable <b>Google Drive API</b> in Library.</li>
                                        <li>Configure <b>OAuth Consent Screen</b> (External).</li>
                                    </ul>
                                </div>
                                <div class="space-y-2">
                                    <p class="font-bold text-indigo-800">2. Credentials & URI</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Create <b>OAuth 2.0 Client ID</b> (Web Application).</li>
                                        <li>Add Redirect URI: <code class="bg-white px-1 py-0.5 rounded border border-indigo-200">{{ route('vaultix.auth.google.callback') }}</code></li>
                                        <li>Copy <b>Client ID</b> and <b>Secret</b> below.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Client ID</label><input type="text" id="gdrive_client_id" name="credentials[client_id]" value="{{ old('credentials.client_id', request('client_id')) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Client Secret</label><input type="password" id="gdrive_client_secret" name="credentials[client_secret]" value="{{ old('credentials.client_secret', request('client_secret')) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                        </div>
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Refresh Token</label>
                                <input type="text" name="credentials[refresh_token]" value="{{ old('credentials.refresh_token', request('refresh_token')) }}" placeholder="Click authorize to generate" class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50">
                            </div>
                            <button type="button" onclick="generateGoogleToken()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition whitespace-nowrap mb-[1px] shadow-sm">Authorize & Get Token</button>
                        </div>
                        <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Folder ID (Optional)</label><input type="text" name="credentials[folder_id]" value="{{ old('credentials.folder_id') }}" placeholder="1abc123..." class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                    </div>

                    <div id="fields-s3" class="provider-fields space-y-4 hidden">
                        <div class="bg-amber-50 border border-amber-100 p-6 rounded-xl space-y-4 mb-6">
                            <div class="flex items-center gap-2 text-amber-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="font-bold uppercase tracking-wider text-sm">AWS S3 Detailed Setup Guide</p>
                            </div>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-xs text-amber-600 list-disc list-inside">
                                <li>Create an IAM user with <b>AmazonS3FullAccess</b> policy.</li>
                                <li>Generate <b>Access Key ID</b> and <b>Secret Access Key</b>.</li>
                                <li>Ensure the <b>Bucket Name</b> is globally unique.</li>
                                <li>Set the correct <b>Region</b> (e.g., us-east-1).</li>
                            </ul>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Access Key</label><input type="text" name="credentials[key]" value="{{ old('credentials.key') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Secret Key</label><input type="password" name="credentials[secret]" value="{{ old('credentials.secret') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Bucket</label><input type="text" name="credentials[bucket]" value="{{ old('credentials.bucket') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Region</label><input type="text" name="credentials[region]" value="{{ old('credentials.region', 'us-east-1') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                        </div>
                    </div>

                    <div id="fields-r2" class="provider-fields space-y-4 hidden">
                        <div class="bg-orange-50 border border-orange-100 p-6 rounded-xl space-y-4 mb-6">
                            <div class="flex items-center gap-2 text-orange-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="font-bold uppercase tracking-wider text-sm">Cloudflare R2 Detailed Setup Guide</p>
                            </div>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-xs text-orange-600 list-disc list-inside">
                                <li>Create bucket in <b>R2 Storage</b> dashboard.</li>
                                <li>Create <b>API Token</b> with "Object Read & Write" permissions.</li>
                                <li>Copy the <b>S3 API Endpoint</b> from bucket settings.</li>
                                <li>Use the Access/Secret keys from the token.</li>
                            </ul>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Access Key</label><input type="text" name="credentials[r2_key]" value="{{ old('credentials.r2_key') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Secret Key</label><input type="password" name="credentials[r2_secret]" value="{{ old('credentials.r2_secret') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                        </div>
                        <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Endpoint (URL)</label><input type="text" name="credentials[endpoint]" value="{{ old('credentials.endpoint') }}" placeholder="https://<id>.r2.cloudflarestorage.com" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                        <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Bucket</label><input type="text" name="credentials[r2_bucket]" value="{{ old('credentials.r2_bucket') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                    </div>

                    <div id="fields-sftp" class="provider-fields space-y-4 hidden">
                        <div class="bg-slate-100 border border-slate-200 p-6 rounded-xl space-y-4 mb-6">
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="font-bold uppercase tracking-wider text-sm">SFTP Detailed Setup Guide</p>
                            </div>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-xs text-slate-600 list-disc list-inside">
                                <li>Ensure the remote server has <b>SSH/SFTP</b> service running.</li>
                                <li>The user must have <b>Write Permissions</b> to the root directory.</li>
                                <li>Port defaults to 22.</li>
                                <li>Use an absolute path for the root folder if needed.</li>
                            </ul>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2"><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Host</label><input type="text" name="credentials[host]" value="{{ old('credentials.host') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Port</label><input type="text" name="credentials[port]" value="{{ old('credentials.port', '22') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Username</label><input type="text" name="credentials[username]" value="{{ old('credentials.username') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                            <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Password</label><input type="password" name="credentials[password]" class="w-full px-4 py-2 rounded-lg border border-slate-200"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Settings -->
                <div class="pt-8 border-t border-slate-100">
                    <label class="block text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        4. Advanced Backup Settings
                    </label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Cloud Folder Name</label>
                            <input type="text" name="custom_folder_name" value="{{ old('custom_folder_name') }}" placeholder="e.g. conference-backups" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            <p class="mt-1 text-[10px] text-slate-400">Leave empty to use project name (slugified). This will be created inside your Folder ID.</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Notification Email</label>
                            <input type="email" name="notification_email" value="{{ old('notification_email') }}" placeholder="admin@example.com" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                            <p class="mt-1 text-[10px] text-slate-400">Where to send success/failure alerts.</p>
                        </div>

                        <div class="md:col-span-2 flex items-center gap-8 pt-2">
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" name="notify_on_success" value="1" class="sr-only" {{ old('notify_on_success', '1') ? 'checked' : '' }}>
                                    <div class="block bg-slate-200 w-10 h-6 rounded-full group-hover:bg-slate-300 transition shadow-inner"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform {{ old('notify_on_success', '1') ? 'translate-x-4' : '' }}"></div>
                                </div>
                                <div class="ml-3 text-sm font-medium text-slate-600">Notify on Success</div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" name="notify_on_failure" value="1" class="sr-only" {{ old('notify_on_failure', '1') ? 'checked' : '' }}>
                                    <div class="block bg-slate-200 w-10 h-6 rounded-full group-hover:bg-slate-300 transition shadow-inner"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform {{ old('notify_on_failure', '1') ? 'translate-x-4' : '' }}"></div>
                                </div>
                                <div class="ml-3 text-sm font-medium text-slate-600">Notify on Failure</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-8 bg-slate-50 border-t flex justify-end gap-3">
                <a href="{{ route('vaultix.index') }}" class="px-6 py-3 text-slate-600 font-semibold text-sm">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">Create Backup Configuration</button>
            </div>
        </div>
    </form>

    <!-- Full Provider Guides (Visible only when selected) -->
    <div id="guide-gdrive" class="provider-fields mt-12 bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="p-6 border-b bg-slate-50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Full Google Drive Setup Guide
            </h3>
        </div>
        <div class="p-8 text-sm text-slate-600 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <p class="font-bold text-slate-900 underline">Step 1: Google Cloud Console</p>
                    <ul class="list-disc list-inside space-y-2">
                        <li>Go to <a href="https://console.cloud.google.com/" target="_blank" class="text-indigo-600 font-bold underline">Google Cloud Console</a>.</li>
                        <li>Create a new project or select an existing one.</li>
                        <li>Search for <strong>"Google Drive API"</strong> and click <strong>Enable</strong>.</li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <p class="font-bold text-slate-900 underline">Step 2: OAuth Consent Screen</p>
                    <ul class="list-disc list-inside space-y-2">
                        <li>Go to <strong>APIs & Services > OAuth consent screen</strong>.</li>
                        <li>Choose <strong>External</strong> and fill in the app name and email.</li>
                        <li>Add scopes: <code>.../auth/drive.file</code> if requested.</li>
                    </ul>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t">
                <div class="space-y-4">
                    <p class="font-bold text-slate-900 underline">Step 3: Create Credentials</p>
                    <ul class="list-disc list-inside space-y-2">
                        <li>Go to <strong>APIs & Services > Credentials</strong>.</li>
                        <li>Click <strong>Create Credentials > OAuth client ID</strong>.</li>
                        <li>Select <strong>Web application</strong> as the type.</li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <p class="font-bold text-slate-900 underline">Step 4: URIs Configuration</p>
                    <ul class="list-disc list-inside space-y-2">
                        <li><strong>Authorized JavaScript Origins</strong>: Paste only the domain (e.g., <code class="bg-slate-100 px-1 py-0.5 rounded">{{ url('/') }}</code>).</li>
                        <li><strong>Authorized Redirect URIs</strong>: Paste the full callback URL: <code class="bg-slate-100 px-1 py-0.5 rounded text-indigo-700">{{ route('vaultix.auth.google.callback') }}</code></li>
                        <li>Click <strong>Create</strong> and copy your Client ID & Secret!</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .selected-provider { border-color: #6366f1 !important; background-color: #f5f3ff !important; }
    .selected-provider span { color: #4f46e5 !important; }
</style>

<script>
    function updateSelection(input, className) {
        // Remove active class from all in group
        document.querySelectorAll('.' + className).forEach(el => {
            el.classList.remove('selected-provider', 'border-indigo-500', 'bg-indigo-50');
        });
        
        // Add active class to parent
        const parent = input.closest('.' + className);
        parent.classList.add('selected-provider', 'border-indigo-500', 'bg-indigo-50');

        // Toggle fields if provider changed
        if (className === 'provider-label') {
            document.querySelectorAll('.provider-fields').forEach(el => el.classList.add('hidden'));
            
            // Show fields div
            const fieldsEl = document.getElementById('fields-' + input.value);
            if (fieldsEl) fieldsEl.classList.remove('hidden');

            // Show long guide div if it exists
            const guideEl = document.getElementById('guide-' + input.value);
            if (guideEl) guideEl.classList.remove('hidden');
        }
    }

    function generateGoogleToken() {
        const clientId = document.getElementById('gdrive_client_id').value;
        const clientSecret = document.getElementById('gdrive_client_secret').value;
        
        if (!clientId || !clientSecret) {
            alert('Please enter Client ID and Client Secret first.');
            return;
        }

        window.location.href = `{{ route('vaultix.auth.google.redirect') }}?client_id=${clientId}&client_secret=${clientSecret}`;
    }

    // Initialize selection colors on load
    window.onload = () => {
        document.querySelectorAll('input:checked').forEach(input => {
            const labelClass = input.parentElement.classList[0];
            updateSelection(input, labelClass);
        });
    };
</script>
@endsection
