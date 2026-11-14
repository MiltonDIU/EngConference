@extends('layouts.admin')
@section('content')
<div class="row mb-5">
    <div class="col-lg-11 mx-auto">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div class="mb-2">
                <h4 class="font-weight-bold text-dark mb-1">
                    <i class="fas fa-user-check mr-2 text-primary"></i> {{ $user->name }}
                </h4>
                <small class="text-muted">{{ $user->email }} &middot; {{ $papers->count() }} {{ \Illuminate\Support\Str::plural('paper', $papers->count()) }} assigned</small>
            </div>
            <a href="{{ route('admin.reviewers.index') }}" class="btn btn-outline-secondary btn-sm px-3 mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to Reviewers
            </a>
        </div>

        {{-- Add papers by Submission ID --}}
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-plus-circle mr-2 text-success"></i> Assign papers</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reviewers.attach', $user->id) }}" method="POST">
                    @csrf
                    <div class="form-row align-items-end">
                        <div class="col-md-6 mb-2">
                            <label class="small font-weight-bold mb-1">Submission IDs</label>
                            <input type="text" name="submission_ids" value="{{ old('submission_ids') }}" required
                                   class="form-control form-control-sm @error('submission_ids') is-invalid @enderror"
                                   placeholder="ABS-26416-014, ABS-26430-036">
                            @error('submission_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="small font-weight-bold mb-1">Session info <span class="text-muted font-weight-normal">(optional)</span></label>
                            <input type="text" name="session_info" value="{{ old('session_info') }}" class="form-control form-control-sm"
                                   placeholder="Same as current session if empty">
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-success btn-sm btn-block"><i class="fas fa-plus mr-1"></i> Assign</button>
                        </div>
                    </div>
                    <small class="text-muted">Separate several IDs with commas or spaces. Note: the next Excel sync replaces manual changes.</small>
                </form>
            </div>
        </div>

        {{-- Assigned papers --}}
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-list mr-2 text-primary"></i> Assigned papers</h6>
                @if($papers->isNotEmpty())
                    <form action="{{ route('admin.reviewers.destroy', $user->id) }}" method="POST"
                          onsubmit="return confirm('Detach all {{ $papers->count() }} papers and remove {{ addslashes($user->name) }} as reviewer?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-user-times mr-1"></i> Detach all &amp; remove reviewer</button>
                    </form>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Session</th>
                                <th>Submission ID</th>
                                <th>Title</th>
                                <th class="text-center" style="width: 130px;">Full Paper</th>
                                <th class="text-center" style="width: 130px;">Presentation</th>
                                <th class="text-center" style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($papers as $paper)
                                @php $isOwnPaper = $paper->user_id === $user->id; @endphp
                                <tr class="{{ $isOwnPaper ? 'table-warning' : '' }}">
                                    <td class="small text-muted text-nowrap">
                                        {{ $paper->pivot->session_no ? '#' . $paper->pivot->session_no : '' }}
                                        <span class="d-block">{{ $paper->pivot->session_info }}</span>
                                    </td>
                                    <td class="font-weight-bold text-nowrap">{{ $paper->submission_id }}</td>
                                    <td>
                                        <a href="{{ route('papers.show', $paper->id) }}" class="text-dark">{{ $paper->title }}</a>
                                        <small class="d-block text-muted">Author: {{ $paper->user?->name ?? 'N/A' }}</small>
                                        @if($isOwnPaper)
                                            <span class="badge badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i> Reviewer's own paper</span>
                                        @endif
                                    </td>
                                    @foreach(['full_paper', 'presentation'] as $type)
                                        <td class="text-center align-middle">
                                            @include('reviewer.partials.file-buttons', ['paper' => $paper, 'type' => $type])
                                        </td>
                                    @endforeach
                                    <td class="text-center text-nowrap align-middle">
                                        <a href="{{ route('papers.show', $paper->id) }}" class="btn btn-sm btn-outline-secondary" title="Details"><i class="fas fa-info-circle"></i></a>
                                        <form action="{{ route('admin.reviewers.detach', [$user->id, $paper->id]) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Detach {{ $paper->submission_id }} from {{ addslashes($user->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-unlink mr-1"></i> Detach</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No papers assigned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.papers.partials.file-preview')
@endsection
