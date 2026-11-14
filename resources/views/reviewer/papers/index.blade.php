@extends('layouts.admin')
@section('content')
<div class="row mb-5">
    <div class="col-lg-11 mx-auto">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <h4 class="font-weight-bold text-dark mb-2">
                <i class="fas fa-clipboard-check mr-2 text-primary"></i> Papers Assigned to You
            </h4>
            <span class="badge badge-primary px-3 py-2 mb-2">
                {{ $papers->count() }} {{ \Illuminate\Support\Str::plural('paper', $papers->count()) }}
                &middot; {{ $sessions->count() }} {{ \Illuminate\Support\Str::plural('session', $sessions->count()) }}
            </span>
        </div>

        <p class="text-muted small mb-4">
            <i class="fas fa-info-circle mr-1"></i>
            You are the session chair for the papers below. Click <strong>View</strong> to read the Full Paper or Presentation right here,
            or <strong>Details</strong> for the abstract and authors.
        </p>

        @foreach($sessions as $sessionPapers)
            @php
                $sessionNo = $sessionPapers->first()->pivot->session_no;
                $info = $sessionPapers->first()->pivot->session_info;
            @endphp
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-chalkboard-teacher mr-2 text-primary"></i>
                        {{ $sessionNo ? 'Session ' . $sessionNo : 'Session' }}
                    </h6>
                    @if($info)
                        <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i> {{ $info }}</small>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th style="width: 150px;">Submission ID</th>
                                    <th>Title</th>
                                    <th class="text-center" style="width: 130px;">Full Paper</th>
                                    <th class="text-center" style="width: 130px;">Presentation</th>
                                    <th class="text-center" style="width: 100px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessionPapers as $paper)
                                    @php
                                        $presenter = $paper->authors->firstWhere('is_presenting_author', true) ?? $paper->authors->first();
                                    @endphp
                                    <tr>
                                        <td class="text-muted">{{ $paper->pivot->position ?? $loop->iteration }}</td>
                                        <td class="font-weight-bold text-nowrap">{{ $paper->submission_id }}</td>
                                        <td>
                                            <a href="{{ route('papers.show', $paper->id) }}" class="text-dark font-weight-bold">{{ $paper->title }}</a>
                                            <small class="d-block text-muted">
                                                @if($presenter)
                                                    <i class="fas fa-user mr-1"></i>{{ $presenter->name }}
                                                    @if($presenter->institution) &middot; {{ $presenter->institution }} @endif
                                                @endif
                                                @if($paper->track)
                                                    <span class="d-block"><i class="fas fa-tag mr-1"></i>{{ $paper->track->name }}</span>
                                                @endif
                                            </small>
                                        </td>
                                        @foreach(['full_paper', 'presentation'] as $type)
                                            <td class="text-center align-middle">
                                                @include('reviewer.partials.file-buttons', ['paper' => $paper, 'type' => $type])
                                            </td>
                                        @endforeach
                                        <td class="text-center align-middle">
                                            <a href="{{ route('papers.show', $paper->id) }}" class="btn btn-sm btn-outline-secondary" title="Abstract, authors and all file versions">
                                                <i class="fas fa-info-circle mr-1"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@include('admin.papers.partials.file-preview')
@endsection
