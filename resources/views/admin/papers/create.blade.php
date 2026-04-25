@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        Submit Abstract
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('papers.store') }}" enctype="multipart/form-data">
            @csrf

            <h4 class="mb-4 text-primary"><strong>Abstract Submission Details</strong></h4>

            <div class="form-group">
                <label class="required" for="paper_title">Paper Title*</label>
                <input class="form-control {{ $errors->has('paper_title') ? 'is-invalid' : '' }}" type="text" name="paper_title" id="paper_title" value="{{ old('paper_title', '') }}" required>
                @if($errors->has('paper_title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('paper_title') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="abstract_text">Abstract (Max 300 words)*</label>
                <textarea class="form-control {{ $errors->has('abstract_text') ? 'is-invalid' : '' }}" name="abstract_text" id="abstract_text" rows="6" oninput="countWords()" required>{{ old('abstract_text') }}</textarea>
                <div id="word_count_display" class="small mt-1 text-muted">Words: <span id="word_count">0</span> / 300</div>
                @if($errors->has('abstract_text'))
                    <div class="invalid-feedback">
                        {{ $errors->first('abstract_text') }}
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="keywords">Keywords (3-5 separated by commas)*</label>
                        <input class="form-control {{ $errors->has('keywords') ? 'is-invalid' : '' }}" type="text" name="keywords" id="keywords" value="{{ old('keywords', '') }}" placeholder="keyword1, keyword2, ..." required oninput="countKeywords('keywords', 'keyword_count')">
                        <div id="keyword_count_display" class="small mt-1 text-muted">Keywords: <span id="keyword_count">0</span> / 5</div>
                        @if($errors->has('keywords'))
                            <div class="invalid-feedback">
                                {{ $errors->first('keywords') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="track_id">Conference Track*</label>
                        <select class="form-control {{ $errors->has('track_id') ? 'is-invalid' : '' }}" name="track_id" id="track_id" required onchange="updateSubTracks()">
                            <option value="">Select Main Track</option>
                            @foreach($tracks as $track)
                                <option value="{{ $track->id }}" {{ old('track_id') == $track->id ? 'selected' : '' }}>{{ $track->name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('track_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('track_id') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="sub_track_id">Sub-Track*</label>
                        <select class="form-control {{ $errors->has('sub_track_id') ? 'is-invalid' : '' }}" name="sub_track_id" id="sub_track_id" required>
                            <option value="">Select Sub-Track</option>
                        </select>
                        @if($errors->has('sub_track_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('sub_track_id') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check {{ $errors->has('is_corresponding_author') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="is_corresponding_author" value="0">
                    <input class="form-check-input" type="checkbox" name="is_corresponding_author" id="is_corresponding_author" value="1" {{ old('is_corresponding_author', 0) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_corresponding_author">I am the corresponding author</label>
                </div>
                @if($errors->has('is_corresponding_author'))
                    <div class="invalid-feedback">
                        {{ $errors->first('is_corresponding_author') }}
                    </div>
                @endif
            </div>

            <hr>
            <h5 class="mb-3"><strong>Co-Authors (If any)</strong></h5>
            <div id="co_authors_container">
                <!-- Dynamic Co-authors will be added here -->
            </div>
            <button type="button" class="btn btn-info btn-sm mb-4" onclick="addCoAuthor()"><i class="fa fa-plus"></i> Add Co-Author</button>

            <hr>
            <h5 class="mb-3"><strong>Declarations</strong></h5>
            <div class="bg-light p-3 border rounded mb-4">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="consent_original" id="consent_original" value="1" required>
                    <label class="form-check-label small" for="consent_original">I confirm that this abstract is original and not published elsewhere.*</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="consent_review" id="consent_review" value="1" required>
                    <label class="form-check-label small" for="consent_review">I agree to the peer-review process of the conference.*</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="consent_acceptance" id="consent_acceptance" value="1" required>
                    <label class="form-check-label small" for="consent_acceptance">If accepted, at least one author will register and present.*</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="consent_no_late_addition" id="consent_no_late_addition" value="1" required>
                    <label class="form-check-label small" for="consent_no_late_addition">No author can be added after the abstract is submitted.*</label>
                </div>
            </div>

            <div class="form-group mb-0">
                <button class="btn btn-success" type="submit">
                    <i class="fa fa-paper-plane"></i> Submit Abstract
                </button>
                <a href="{{ route('show-profile') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>

<template id="co_author_template">
    <div class="co-author-entry border p-3 mb-3 rounded position-relative bg-white shadow-sm">
        <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 10px; right: 10px;" onclick="removeCoAuthor(this)"><i class="fa fa-times"></i></button>
        <div class="d-flex justify-content-between align-items-center mb-3 pr-5">
            <h6 class="mb-0 font-weight-bold text-secondary text-uppercase" style="font-size: 0.8rem;">Co-Author Entry</h6>
            <div class="d-flex align-items-center">
                <input type="radio" id="presenting_{index}" name="presenting_author_index" value="{index}" class="mr-2" style="cursor: pointer; transform: scale(1.2);" required>
                <label for="presenting_{index}" class="mb-0 text-secondary font-weight-bold" style="font-size: 0.85rem; cursor: pointer;">Presenting Author</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <input type="text" name="co_authors[{index}][name]" class="form-control form-control-sm" placeholder="Full Name*" required>
            </div>
            <div class="col-md-6 mb-2">
                <input type="email" name="co_authors[{index}][email]" class="form-control form-control-sm" placeholder="Email*" required>
            </div>
            <div class="col-md-3 mb-2">
                <input type="text" name="co_authors[{index}][designation]" class="form-control form-control-sm" placeholder="Designation*" required>
            </div>
            <div class="col-md-3 mb-2">
                <input type="text" name="co_authors[{index}][department]" class="form-control form-control-sm" placeholder="Department*" required>
            </div>
            <div class="col-md-3 mb-2">
                <input type="text" name="co_authors[{index}][institution]" class="form-control form-control-sm" placeholder="Institution*" required>
            </div>
            <div class="col-md-3 mb-2">
                <select name="co_authors[{index}][country_id]" class="form-control form-control-sm" required>
                    <option value="">Country*</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</template>

@endsection

@push('script')
<script>
    let coAuthorIndex = 0;

    const tracksData = {!! $tracks->toJson() !!};

    function updateSubTracks() {
        const trackSelect = document.getElementById('track_id');
        const subTrackSelect = document.getElementById('sub_track_id');
        const selectedTrackId = trackSelect.value;
        const oldSubTrackId = "{{ old('sub_track_id') }}";

        // Clear sub-track options
        subTrackSelect.innerHTML = '<option value="">Select Sub-Track</option>';

        if (selectedTrackId) {
            const selectedTrack = tracksData.find(t => t.id == selectedTrackId);
            if (selectedTrack && selectedTrack.sub_tracks) {
                selectedTrack.sub_tracks.forEach(subTrack => {
                    const option = document.createElement('option');
                    option.value = subTrack.id;
                    option.textContent = subTrack.name;
                    if (subTrack.id == oldSubTrackId) {
                        option.selected = true;
                    }
                    subTrackSelect.appendChild(option);
                });
            }
        }
    }

    function countKeywords(inputId, countId) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(countId);
        const display = document.getElementById(inputId + '_count_display');

        const keywords = input.value ? input.value.split(',').map(k => k.trim()).filter(k => k !== '') : [];
        const count = keywords.length;

        counter.innerText = count;

        if (count < 3 || count > 5) {
            display.classList.remove('text-muted');
            display.classList.add('text-danger', 'font-weight-bold');
        } else {
            display.classList.remove('text-danger', 'font-weight-bold');
            display.classList.add('text-muted');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSubTracks();
        countWords();
        countKeywords('keywords', 'keyword_count');

        // Automatically add the submitter as the primary author
        const currentUserData = {
            name: @json(trim((Auth::user()->profile->first_name ?? '') . ' ' . (Auth::user()->profile->last_name ?? '')) ?: (Auth::user()->name ?? '')),
            email: @json(Auth::user()->email ?? ''),
            designation: @json(Auth::user()->profile->designation ?? ''),
            department: @json(Auth::user()->profile->department ?? ''),
            institution: @json(Auth::user()->profile->institution ?? ''),
            country_id: @json(Auth::user()->profile->country_id ?? '')
        };
        addCoAuthor(currentUserData, true);
    });

    function addCoAuthor(data = null, isPrimary = false) {
        const container = document.getElementById('co_authors_container');
        const template = document.getElementById('co_author_template').innerHTML;
        const html = template.replace(/{index}/g, coAuthorIndex);

        const div = document.createElement('div');
        div.innerHTML = html;

        if (isPrimary) {
            const btn = div.querySelector('.btn-danger');
            if(btn) btn.style.display = 'none';
        }

        if (coAuthorIndex === 0) {
            const radioBtn = div.querySelector(`input[name="presenting_author_index"]`);
            if (radioBtn) radioBtn.checked = true;
        }

        if (data && data.name) {
            div.querySelector(`input[name="co_authors[${coAuthorIndex}][name]"]`).value = data.name || '';
            div.querySelector(`input[name="co_authors[${coAuthorIndex}][email]"]`).value = data.email || '';
            div.querySelector(`input[name="co_authors[${coAuthorIndex}][designation]"]`).value = data.designation || '';
            div.querySelector(`input[name="co_authors[${coAuthorIndex}][department]"]`).value = data.department || '';
            div.querySelector(`input[name="co_authors[${coAuthorIndex}][institution]"]`).value = data.institution || '';

            if (data.country_id) {
                const countrySelect = div.querySelector(`select[name="co_authors[${coAuthorIndex}][country_id]"]`);
                const option = Array.from(countrySelect.options).find(opt => opt.value == data.country_id);
                if (option) option.selected = true;
            }
        }

        container.appendChild(div.firstElementChild);
        coAuthorIndex++;
    }

    function removeCoAuthor(btn) {
        btn.closest('.co-author-entry').remove();
    }
</script>
@endpush
