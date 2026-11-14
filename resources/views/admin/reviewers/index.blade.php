@extends('layouts.admin')
@section('content')
<div class="row mb-5">
    <div class="col-lg-11 mx-auto">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h4 class="font-weight-bold text-dark mb-2">
                <i class="fas fa-user-check mr-2 text-primary"></i> Reviewers (Session Chairs)
            </h4>
            <div class="mb-2">
                <span class="badge badge-primary px-3 py-2">{{ $reviewers->count() }} reviewers</span>
                <span class="badge badge-info px-3 py-2">{{ $reviewers->sum('review_papers_count') }} papers assigned</span>
                <span class="badge badge-{{ $unassignedCount ? 'warning' : 'light border' }} px-3 py-2">{{ $unassignedCount }} approved papers without reviewer</span>
            </div>
        </div>

        @if($ownPapers->isNotEmpty())
            <div class="alert alert-warning shadow-sm border-0 mb-4">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong>{{ $ownPapers->count() }} {{ \Illuminate\Support\Str::plural('reviewer', $ownPapers->count()) }}</strong>
                {{ $ownPapers->count() === 1 ? 'has' : 'have' }} their own paper in their review list
                ({{ $ownPapers->flatten(1)->pluck('submission_id')->implode(', ') }}).
                They are highlighted and listed first below. Detach the paper or change the chair in the Excel sheet.
            </div>
        @endif

        {{-- Sync from Excel --}}
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-file-excel mr-2 text-success"></i> Sync from Excel sheet</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">
                    The sheet is the full list: every chair gets exactly the ABS IDs of their session(s); chairs missing from the sheet lose their papers.
                    New emails get an account (they set their password with <em>Forgot Password</em>).
                    <strong>Manual changes made below are overwritten by the next sync.</strong>
                </p>
                <form action="{{ route('admin.reviewers.sync') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row align-items-end">
                        <div class="col-md-5 mb-2">
                            <label class="small font-weight-bold mb-1">Excel file (.xlsx)</label>
                            <input type="file" name="sheet_file" accept=".xlsx" class="form-control-file">
                            <small class="text-muted">
                                @if($defaultSheetExists)
                                    Leave empty to use the file in <code>public/documents</code>.
                                @else
                                    Default file not found, please upload one.
                                @endif
                            </small>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold mb-1">Sheet name</label>
                            <input type="text" name="sheet_name" value="All Sessions" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4 mb-2">
                            <button type="submit" name="dry_run" value="1" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-search mr-1"></i> Preview changes
                            </button>
                        </div>
                    </div>
                </form>

                @if(session('sync_summary'))
                    @php $s = session('sync_summary'); @endphp
                    <div class="alert {{ $s['dry_run'] ? 'alert-info' : 'alert-success' }} mt-3 mb-0">
                        <h6 class="font-weight-bold mb-2">
                            {{ $s['dry_run'] ? 'Preview (nothing saved yet)' : 'Sync completed' }}
                            <small class="text-muted font-weight-normal">· {{ $s['file'] }}</small>
                        </h6>
                        <div class="row small">
                            <div class="col-md-4">
                                Sessions in sheet: <strong>{{ $s['sessions'] }}</strong><br>
                                Reviewers (existing users): <strong>{{ $s['users_existing'] }}</strong><br>
                                New accounts: <strong>{{ count($s['users_created']) }}</strong>
                            </div>
                            <div class="col-md-4">
                                Papers assigned (total): <strong>{{ $s['assignments'] }}</strong><br>
                                Newly added: <strong class="text-success">{{ $s['added'] }}</strong><br>
                                Removed: <strong class="text-danger">{{ $s['removed'] }}</strong>
                            </div>
                            <div class="col-md-4">
                                Reviewers removed: <strong>{{ count($s['reviewers_removed']) }}</strong><br>
                                Sessions without email: <strong>{{ count($s['sessions_without_email']) }}</strong><br>
                                ABS IDs not found: <strong>{{ count($s['missing_papers']) }}</strong>
                            </div>
                        </div>
                        @foreach([
                            'users_created' => 'New accounts',
                            'reviewers_removed' => 'No longer reviewers',
                            'sessions_without_email' => 'Sessions without chair email (papers not assigned)',
                            'missing_papers' => 'ABS IDs not found',
                            'invalid_emails' => 'Invalid emails skipped',
                            'trashed_users' => 'Deleted accounts skipped',
                        ] as $key => $label)
                            @if(!empty($s[$key]))
                                <details class="small mt-2">
                                    <summary>{{ $label }} ({{ count($s[$key]) }})</summary>
                                    <div class="mt-1 text-break">{{ implode(', ', $s[$key]) }}</div>
                                </details>
                            @endif
                        @endforeach

                        @if($s['dry_run'] && $storedSheet)
                            <form action="{{ route('admin.reviewers.sync') }}" method="POST" class="mt-3"
                                  onsubmit="return confirm('Apply these changes to the reviewer assignments?');">
                                @csrf
                                <input type="hidden" name="use_stored" value="1">
                                <input type="hidden" name="dry_run" value="0">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-sync-alt mr-1"></i> Confirm sync
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Reviewer list --}}
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-users mr-2 text-primary"></i> Reviewers</h6>
                <input type="search" id="reviewer-search" class="form-control form-control-sm mt-2 mt-md-0" style="max-width: 260px;" placeholder="Search name or email">
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="reviewer-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Sessions</th>
                                <th class="text-center">Papers</th>
                                <th class="text-center">Author?</th>
                                <th class="text-center" style="width: 170px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviewers as $reviewer)
                                @php $own = $ownPapers[$reviewer->id] ?? collect(); @endphp
                                <tr class="{{ $own->isNotEmpty() ? 'table-warning' : '' }}">
                                    <td class="font-weight-bold">
                                        {{ $reviewer->name }}
                                        @foreach($own as $p)
                                            <a href="{{ route('papers.show', $p->id) }}" class="badge badge-warning d-inline-block mt-1" title="This reviewer is the author of this paper">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Own paper: {{ $p->submission_id }}
                                            </a>
                                        @endforeach
                                    </td>
                                    <td>{{ $reviewer->email }}</td>
                                    <td class="small text-muted">{!! ($sessionLabels[$reviewer->id] ?? collect())->map(fn ($l) => e($l))->implode('<br>') !!}</td>
                                    <td class="text-center"><span class="badge badge-info">{{ $reviewer->review_papers_count }}</span></td>
                                    <td class="text-center">
                                        @if($reviewer->roles->contains('id', 3))
                                            <span class="badge badge-light border">Author</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="{{ route('admin.reviewers.show', $reviewer->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-list mr-1"></i> Papers
                                        </a>
                                        <form action="{{ route('admin.reviewers.destroy', $reviewer->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Remove {{ addslashes($reviewer->name) }} as reviewer? All {{ $reviewer->review_papers_count }} assigned papers will be detached.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove reviewer">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No reviewers yet. Use "Sync from Excel sheet" above.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('reviewer-search').addEventListener('input', function () {
        var term = this.value.toLowerCase();
        document.querySelectorAll('#reviewer-table tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().indexOf(term) === -1 ? 'none' : '';
        });
    });
</script>
@endsection
