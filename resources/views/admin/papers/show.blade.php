@extends('layouts.admin')
@section('content')
<div class="row mb-5">
    <div class="col-md-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="font-weight-bold text-dark">
                <i class="fas fa-file-invoice mr-2 text-primary"></i> Abstract Details
            </h4>
            <div class="btn-group shadow-sm">
                <a href="{{ route('papers.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
                @can('paper_access')
                    <button type="button" class="btn btn-outline-primary btn-sm px-3 ml-1" data-toggle="modal" data-target="#reviewModal">
                        @if($paper->status == 'pending')
                            <i class="fas fa-gavel mr-1"></i> Review Abstract
                        @else
                            <i class="fas fa-edit mr-1"></i> Update Decision
                        @endif
                    </button>
                @endcan
                @can('paper_edit')
                <a href="#" class="btn btn-outline-info btn-sm px-3">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                @endcan
            </div>
        </div>

        <!-- Abstract Content Card -->
        <div class="card shadow-sm border-0 mb-4 rounded-lg">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="badge badge-primary px-3 py-2 text-uppercase mb-2 shadow-none" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        Submission ID: {{ $paper->submission_id }}
                    </span>
                    @php
                        $statusClass = [
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger'
                        ][$paper->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $statusClass }} px-3 py-1 text-uppercase small">
                        Status: {{ $paper->status }}
                    </span>
                </div>
                <h5 class="font-weight-bold text-dark-blue mt-2 lh-1-4">
                    {{ $paper->title }}
                </h5>
            </div>
            <div class="card-body py-4 px-4 bg-light-soft">
@if($paper->review_note)
                <div class="alert alert-{{ $paper->status == 'approved' ? 'success' : ($paper->status == 'rejected' ? 'danger' : 'info') }} mb-4 border-{{ $paper->status == 'approved' ? 'success' : ($paper->status == 'rejected' ? 'danger' : 'info') }} shadow-sm" style="border-left-width: 4px; border-radius: 8px;">
                    <h6 class="alert-heading font-weight-bold mb-2"><i class="fas fa-comment-dots mr-2"></i> Current Reviewer Note</h6>
                    <p class="mb-2 mt-2" style="font-size: 0.95rem; line-height: 1.5;">{{ $paper->review_note }}</p>
                    @if($paper->reviewed_at)
                    <hr class="my-2 opacity-25">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted italic">Last updated: {{ $paper->reviewed_at->format('M d, Y h:i A') }}</small>
                        <small class="font-weight-bold">By: {{ $paper->reviewer->name ?? 'Admin' }}</small>
                    </div>
                    @endif
                </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold d-block mb-1" style="font-size: 0.65rem;">Track Name</small>
                        <span class="p-2 px-3 border rounded-pill bg-white text-dark small font-weight-600">
                            <i class="fas fa-layer-group mr-2 text-info opacity-7"></i> {{ $paper->track->name ?? 'N/A' }}
                        </span>
                    </div>
                    @if($paper->subTrack)
                    <div class="col-md-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold d-block mb-1" style="font-size: 0.65rem;">Sub-Track</small>
                        <span class="p-2 px-3 border rounded-pill bg-white text-dark small font-weight-600">
                            <i class="fas fa-chevron-right mr-2 text-success opacity-7"></i> {{ $paper->subTrack->name }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="mb-4">
                    <h6 class="font-weight-bold text-muted text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.8px;">
                        <i class="fas fa-align-left mr-2"></i> Abstract / Summary
                    </h6>
                    <div class="p-4 bg-white border rounded text-dark lh-1-7 shadow-xs" style="white-space: pre-wrap; font-size: 1.05rem;">{{ $paper->abstract }}</div>
                </div>

                <div class="mb-2">
                    <h6 class="font-weight-bold text-muted text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.8px;">
                        <i class="fas fa-tags mr-2"></i> Keywords
                    </h6>
                    <div class="d-flex flex-wrap">
                        @foreach(explode(',', $paper->keywords) as $keyword)
                            <span class="badge badge-white border text-dark px-3 mt-1 py-2 rounded-lg mr-2 shadow-xs" style="font-weight: 500;">
                                {{ trim($keyword) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Authors Section -->
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-users mr-2 text-primary"></i> Contributing Authors
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-gray-50 text-muted small font-weight-bold">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="py-3">Name & Designation</th>
                                <th class="py-3">Email & Institution</th>
                                <th class="py-3 text-center">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paper->authors->sortBy('author_order') as $author)
                            <tr>
                                <td class="px-4 py-3 text-muted small font-weight-bold">{{ $loop->iteration }}</td>
                                <td class="py-3">
                                    <div class="font-weight-bold text-dark">{{ $author->name }}</div>
                                    <small class="text-info">{{ $author->designation }}</small>
                                </td>
                                <td class="py-3">
                                    <div class="text-dark small"><i class="far fa-envelope mr-1 text-muted"></i> {{ $author->email }}</div>
                                    <div class="text-muted small"><i class="fas fa-university mr-1 text-muted"></i> {{ $author->institution }} ({{ $author->country->name ?? 'N/A' }})</div>
                                </td>
                                <td class="py-3 text-center">
                                    @if($author->is_presenting_author)
                                        <span class="badge badge-success px-2 py-1 small rounded shadow-none">Presenting</span>
                                    @else
                                        <span class="badge badge-light border px-2 py-1 small rounded text-muted">Co-Author</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($paper->reviewHistory && $paper->reviewHistory->count() > 0)
        <!-- Review History Section -->
        <div class="card shadow-sm border-0 rounded-lg mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-history mr-2 text-primary"></i> Review & Approval History
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-gray-50 text-muted small font-weight-bold">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Reviewer Note</th>
                                <th class="py-3 text-center">Reviewed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paper->reviewHistory as $history)
                            <tr>
                                <td class="px-4 py-3 text-muted small font-weight-bold">{{ $history->created_at->format('M d, Y h:i A') }}</td>
                                <td class="py-3">
                                    <span class="badge badge-{{ $history->status == 'approved' ? 'success' : ($history->status == 'rejected' ? 'danger' : 'secondary') }} px-2 py-1 small rounded shadow-none text-uppercase">
                                        {{ $history->status }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="text-dark small">{{ $history->review_note ?: 'No note provided' }}</div>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="text-dark font-weight-bold small">{{ $history->reviewer->name ?? 'Admin' }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@can('paper_access')
<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">
                    {{ $paper->status == 'pending' ? 'Review Abstract' : 'Update Review Decision' }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.papers.review', $paper->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status" class="font-weight-bold">Decision Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Select Decision</option>
                            <option value="approved" {{ $paper->status == 'approved' ? 'selected' : '' }}>Approve Abstract</option>
                            <option value="rejected" {{ $paper->status == 'rejected' ? 'selected' : '' }}>Reject Abstract</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="review_note" class="font-weight-bold">Reviewer Note (Optional)</label>
                        <textarea name="review_note" id="review_note" rows="4" class="form-control" placeholder="Add a note explaining the decision... This note will be sent to the author.">{{ $paper->review_note }}</textarea>
                        <small class="form-text text-muted">This note will be included in the email sent to the corresponding author.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Decision</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<style>
    .rounded-lg { border-radius: 12px !important; }
    .text-dark-blue { color: #1a2a47; }
    .bg-light-soft { background-color: #fcfdfe; }
    .lh-1-7 { line-height: 1.7; }
    .lh-1-4 { line-height: 1.4; }
    .font-weight-600 { font-weight: 600; }
    .opacity-7 { opacity: 0.7; }
    .bg-gray-50 { background-color: #f9fafb; }
    .shadow-xs { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
</style>
@endsection
