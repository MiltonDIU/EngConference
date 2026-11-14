{{-- Quick View / Download of the latest version of the Full Paper or Presentation, straight from a list.
     Needs admin.papers.partials.file-preview on the page. Expects $paper (with files) and $type. --}}
@php
    $latest = $paper->latestFile($type);
    $label = \App\Services\PaperFileService::TYPES[$type]['label'];
@endphp
@if($latest)
    <div class="text-nowrap">
        @if(\App\Services\PaperFileService::isPreviewable($latest))
            <button type="button" class="btn btn-sm btn-info paper-file-preview-btn" title="View {{ $label }}"
                    data-kind="{{ \App\Services\PaperFileService::previewKind($latest) }}"
                    data-url="{{ route('admin.papers.files.preview', [$paper->id, $latest->id]) }}"
                    data-download-url="{{ route('admin.papers.files.download', [$paper->id, $latest->id]) }}"
                    data-name="{{ $latest->file_name }}">
                <i class="fas fa-eye mr-1"></i> View
            </button>
        @endif
        <a href="{{ route('admin.papers.files.download', [$paper->id, $latest->id]) }}" class="btn btn-sm btn-outline-primary" title="Download {{ $label }}">
            <i class="fas fa-download"></i>
        </a>
    </div>
@else
    <span class="badge badge-light border text-muted">Not submitted</span>
@endif
