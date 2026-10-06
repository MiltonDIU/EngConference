{{-- Highlight for authors whose approved & paid papers still need a Full Paper / Presentation --}}
@php
    $pendingFileUploads = \App\Services\PaperFileService::pendingUploadsFor(auth()->user());
    $fileDeadline = trim((string) (\App\Models\Setting::where('key', 'paper_file_submission_deadline')->value('value') ?? ''));
@endphp
@if($pendingFileUploads->isNotEmpty())
    <div class="card mb-4 border-success shadow-sm" style="border-width: 2px; border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-success text-white py-3">
            <h5 class="card-title font-weight-bold mb-0">
                <i class="fas fa-file-upload mr-2"></i> Submit Your Full Paper & Presentation
            </h5>
        </div>
        <div class="card-body bg-white p-4">
            <p class="text-muted mb-3">
                Full paper and presentation submission is now open for your approved abstract(s).
                Upload the <strong>Full Paper (.doc / .docx)</strong> and the <strong>Presentation (.ppt / .pptx)</strong> for each paper below.
                @if($fileDeadline !== '')
                    <br><strong class="text-danger"><i class="far fa-clock mr-1"></i> Deadline: {{ \Carbon\Carbon::parse($fileDeadline)->format('M d, Y') }}</strong>
                @endif
            </p>

            @foreach($pendingFileUploads as $item)
                <div class="d-flex justify-content-between align-items-center flex-wrap p-3 mb-2 bg-light rounded border">
                    <div class="mb-2 mb-md-0 mr-3" style="min-width: 0;">
                        <h6 class="font-weight-bold text-primary mb-1">
                            <i class="fas fa-file-alt mr-1"></i> Paper ID: {{ $item->paper->submission_id }}
                        </h6>
                        <div class="text-dark small mb-2">{{ $item->paper->title }}</div>
                        @foreach($item->slots as $slot)
                            @if($slot['latest'])
                                <span class="badge badge-success mr-1"><i class="fas fa-check mr-1"></i> {{ $slot['label'] }} submitted</span>
                            @else
                                <span class="badge badge-warning mr-1"><i class="fas fa-exclamation-circle mr-1"></i> {{ $slot['label'] }} pending</span>
                            @endif
                        @endforeach
                    </div>
                    <a href="{{ route('papers.show', $item->paper->id) }}#paper-files" class="btn btn-success shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-upload mr-1"></i> Upload Files
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
