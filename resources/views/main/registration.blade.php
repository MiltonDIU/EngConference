@extends('layouts.main')

@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section" style="background: linear-gradient(to bottom, black, black)">
                <div class="container">
                    <div class="section-header">
                        <h3>Register Now</h3>
                        <p style="color: red">**Notice:** This registration form is currently under testing. Please do not submit any actual registration information at this time, as all submitted data will be deleted. The official registration will open soon.
                        </p>
                    </div>
                </div>
            </div>
            <div class="container">
                @if(session()->has('message') || session()->has('success'))
                    <div class="alert alert-success alert-dismissible">
                        <strong>Success!</strong> {{ session()->get('message') ?? session()->get('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <strong>Error!</strong> {{ session()->get('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session()->has('warning'))
                    <div class="alert alert-warning alert-dismissible">
                        <strong>Note!</strong> {{ session()->get('warning') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session()->has('info'))
                    <div class="alert alert-info alert-dismissible">
                        <strong>Information!</strong> {{ session()->get('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @php
                    $eventStartDate = \Carbon\Carbon::parse($settings['registration_start_date'] ?? now());
                    $eventCloseDate = \Carbon\Carbon::parse($settings['registration_close_date'] ?? now()->addMonth());
                    $eventEarlyRegDate = \Carbon\Carbon::parse($settings['early_registration_last_date'] ?? now()->addWeek());
                    $currentDate = \Carbon\Carbon::now();
                @endphp

                @if ($currentDate < $eventStartDate)
                    <div class="row">
                        <h1 class="text-center">The event registration has not started yet. It will start on {{ $eventStartDate->format('Y-m-d H:i:s') }}</h1>
                    </div>
                @elseif ($currentDate >= $eventStartDate && $currentDate <= $eventCloseDate)

                    @if(($settings['seat_is_full'] ?? 'false') == 'false')
                        <div class="row">
                            <div class="col-md-5 line">
                                <div class="bg-color">
{{--                                    <h4><strong> {!! $settings['title'] ?? 'Conference Title' !!}</strong></h4>--}}
{{--                                    <span class="main-title">International Conference on</span>--}}
{{--                                    <img src="{{ asset('/') }}img/eng-con_logo.png">--}}
                                    <span class="second-title">International Conference on Beyond Nature and Culture </span>
                                    <span class="sub-title">Planetarity Precarity in Literary-Cultural-Linguistic Representations</span>


                                    <div><img width="20px;" src="{{ asset('/') }}img/calendar.png"> {!! $settings['about_when'] ?? '' !!} </div>
                                    <div><img width="20px;" src="{{ asset('/') }}img/clock.png"> 08:30 AM - 06:00 PM</div>
                                    <div style="color:#000000;"><img width="20px;" src="{{ asset('/') }}img/location.png">Location: {{ $settings['about_where'] ?? '' }}</div>
                                    <br/>
                                    <div class="fee-information">
                                        <div class="table-responsive">
                                            <table class="table table-sm fee-table shadow-sm text-center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2" style="vertical-align: middle;">Currency</th>
                                                        <th colspan="2" class="bg-light">Author</th>
                                                        <th rowspan="2" style="vertical-align: middle;">Participant</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Early Bird</th>
                                                        <th>Regular</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><strong>USD</strong></td>
                                                        <td class="text-primary font-weight-bold">{{ $settings['usd_earlybird_price'] ?? '0' }}</td>
                                                        <td>{{ $settings['usd_regular_price'] ?? '0' }}</td>
                                                        <td class="text-success font-weight-bold">{{ $settings['usd_participant_price'] ?? '0' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>BDT</strong></td>
                                                        <td class="text-primary font-weight-bold">{{ $settings['bdt_earlybird_price'] ?? '0' }}</td>
                                                        <td>{{ $settings['bdt_regular_price'] ?? '0' }}</td>
                                                        <td class="text-success font-weight-bold">{{ $settings['bdt_participant_price'] ?? '0' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>INR</strong></td>
                                                        <td class="text-primary font-weight-bold">{{ $settings['inr_earlybird_price'] ?? '0' }}</td>
                                                        <td>{{ $settings['inr_regular_price'] ?? '0' }}</td>
                                                        <td class="text-success font-weight-bold">{{ $settings['inr_participant_price'] ?? '0' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>EUR</strong></td>
                                                        <td class="text-primary font-weight-bold">{{ $settings['eur_earlybird_price'] ?? '0' }}</td>
                                                        <td>{{ $settings['eur_regular_price'] ?? '0' }}</td>
                                                        <td class="text-success font-weight-bold">{{ $settings['eur_participant_price'] ?? '0' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr>
                                        <div><strong>Reg. Starting: {{ $eventStartDate->isoFormat('D MMMM YYYY HH:mm:ss') }} </strong></div>
                                        <div><strong>Early Deadline: {{ $eventEarlyRegDate->isoFormat('D MMMM YYYY HH:mm:ss') }} </strong></div>
                                        <div><strong>Reg. Deadline: {{ $eventCloseDate->isoFormat('D MMMM YYYY HH:mm:ss') }} </strong></div>
                                    </div>
                                    <br/>

{{--                                    <h5><strong>Participation Benefits</strong> </h5>--}}
{{--                                    <ul>--}}
{{--                                        @isset($aminities)--}}
{{--                                            @foreach($aminities as $aminity)--}}
{{--                                                <li>{{ $aminity->name }}</li>--}}
{{--                                            @endforeach--}}
{{--                                        @endisset--}}
{{--                                    </ul>--}}

{{--                                    <hr>--}}
{{--                                    <div style="text-align:justify;">--}}
{{--                                        {!! $settings['about_description'] ?? '' !!}--}}
{{--                                    </div>--}}
                                </div>
                            </div>

                            <div class="col-md-7 line">
                                <div class="bg-color-form">
                                    <form method="POST" action="{{ route('register') }}" id="registrationForm">
                                        @csrf

                                        <h4 class="mb-4 text-primary"><strong>Participant Information</strong></h4>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="first_name"><strong>First Name*</strong></label>
                                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autofocus>
                                                @error('first_name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="last_name"><strong>Last Name*</strong></label>
                                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required>
                                                @error('last_name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="email"><strong>Email Address*</strong></label>
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                                @error('email') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="password"><strong>Password*</strong></label>
                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                                @error('password') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="password-confirm"><strong>Confirm Password*</strong></label>
                                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="designation"><strong>Designation*</strong></label>
                                                <input type="text" id="designation" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" required>
                                                @error('designation') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="department"><strong>Department</strong></label>
                                                <input type="text" id="department" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department') }}">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="institution"><strong>Institution / Affiliation*</strong></label>
                                            <input type="text" id="institution" name="institution" class="form-control @error('institution') is-invalid @enderror" value="{{ old('institution') }}" required>
                                            @error('institution') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="country_id"><strong>Country*</strong></label>
                                                <select id="country_id" name="country_id" class="form-control @error('country_id') is-invalid @enderror" required>
                                                    <option value="">Select Country</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('country_id') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="whatsapp_number"><strong>WhatsApp Number*</strong></label>
                                                <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror" value="{{ old('whatsapp_number') }}" required>
                                                @error('whatsapp_number') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label><strong>Mode of Participation*</strong></label><br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="participation_mode" id="onsite" value="onsite" {{ old('participation_mode', 'onsite') == 'onsite' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="onsite">Onsite</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="participation_mode" id="online" value="online" {{ old('participation_mode') == 'online' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="online">Online</label>
                                            </div>
                                        </div>

                                        <div class="mb-4 p-3 bg-light rounded border">
                                            <label><strong>I want to:*</strong></label><br>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="is_author" id="participant_only" value="0" {{ old('is_author', '0') == '0' ? 'checked' : '' }} onchange="toggleAbstractSection()">
                                                <label class="form-check-label" for="participant_only">Register as Participant Only (Payment Required)</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="is_author" id="submit_abstract" value="1" {{ old('is_author') == '1' ? 'checked' : '' }} onchange="toggleAbstractSection()">
                                                <label class="form-check-label" for="submit_abstract">
                                                    @if(($settings['is_abstract_submission_open'] ?? 'true') == 'true')
                                                        Submit an Abstract (Payment Required after Abstract Confirmation)
                                                    @else
                                                        Register as Paper Author (Submit abstract later after email verification)
                                                    @endif
                                                </label>
                                            </div>
                                            @if(($settings['is_abstract_submission_open'] ?? 'true') == 'false')
                                                <div class="mt-2 text-danger small font-weight-bold">
{{--                                                    <i class="fa fa-info-circle"></i> Abstract submission is currently closed.--}}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Abstract Section -->
                                        <div id="abstract_section" style="display: none;">
                                            <h4 class="mb-4 text-primary"><strong>Abstract Submission Details</strong></h4>

                                            <div class="mb-3">
                                                <label for="paper_title"><strong>Paper Title*</strong></label>
                                                <input type="text" id="paper_title" name="paper_title" class="form-control" value="{{ old('paper_title') }}">
                                                @error('paper_title') <span class="text-danger small"><strong>{{ $message }}</strong></span> @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="abstract_text"><strong>Abstract (Max 300 words)*</strong></label>
                                                <textarea id="abstract_text" name="abstract_text" class="form-control" rows="6" oninput="countWords()">{{ old('abstract_text') }}</textarea>
                                                <div id="word_count_display" class="small mt-1 text-muted">Words: <span id="word_count">0</span> / 300</div>
                                                @error('abstract_text') <span class="text-danger small"><strong>{{ $message }}</strong></span> @enderror
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="keywords"><strong>Keywords (3-5 separated by commas)*</strong></label>
                                                    <input type="text" id="keywords" name="keywords" class="form-control" placeholder="keyword1, keyword2, ..." value="{{ old('keywords') }}" oninput="countKeywords('keywords', 'keyword_count')">
                                                    <div id="keyword_count_display" class="small mt-1 text-muted">Keywords: <span id="keyword_count">0</span> / 5</div>
                                                    @error('keywords') <span class="text-danger small"><strong>{{ $message }}</strong></span> @enderror
                                                </div>
 Bond
                                                <div class="col-md-6 mb-3">
                                                    <label for="track_id"><strong>Conference Track*</strong></label>
                                                    <select id="track_id" name="track_id" class="form-control" onchange="updateSubTracks()">
                                                        <option value="">Select Track</option>
                                                        @foreach($tracks as $track)
                                                            <option value="{{ $track->id }}" {{ old('track_id') == $track->id ? 'selected' : '' }}>{{ $track->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('track_id') <span class="text-danger small"><strong>{{ $message }}</strong></span> @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="sub_track_id"><strong>Sub-Theme*</strong></label>
                                                    <select id="sub_track_id" name="sub_track_id" class="form-control">
                                                        <option value="">Select Sub-Theme</option>
                                                    </select>
                                                    @error('sub_track_id') <span class="text-danger small"><strong>{{ $message }}</strong></span> @enderror
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_corresponding_author" id="is_corresponding_author" value="1" {{ old('is_corresponding_author') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_corresponding_author">I am the corresponding author</label>
                                                </div>
                                            </div>

                                            <div class="mb-4 p-2 bg-light rounded border">
                                                <div class="form-check">
                                                    <input class="form-check-input presenting-author-radio" type="radio" name="presenting_author_index" id="presenter_submitter" value="submitter" {{ old('presenting_author_index', 'submitter') == 'submitter' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="presenter_submitter"><strong>I will be the Presenting Author</strong></label>
                                                </div>
                                            </div>

                                            <h5 class="mb-3"><strong>Co-Authors</strong></h5>
                                            <div id="co_authors_container">
                                                <!-- Dynamic Co-authors will be added here -->
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-4" onclick="addCoAuthor()">+ Add Co-Author</button>

                                            <h5 class="mb-3"><strong>Declarations</strong></h5>
                                            <div class="bg-light p-3 border rounded mb-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input @error('consent_original') is-invalid @enderror" type="checkbox" name="consent_original" id="consent_original" value="1" required>
                                                    <label class="form-check-label small" for="consent_original">I confirm that this abstract is original and not plastered elsewhere.*</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input @error('consent_review') is-invalid @enderror" type="checkbox" name="consent_review" id="consent_review" value="1" required>
                                                    <label class="form-check-label small" for="consent_review">I agree to the peer-review process of the conference.*</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input @error('consent_acceptance') is-invalid @enderror" type="checkbox" name="consent_acceptance" id="consent_acceptance" value="1" required>
                                                    <label class="form-check-label small" for="consent_acceptance">If accepted, at least one author will register and present.*</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input @error('consent_no_late_addition') is-invalid @enderror" type="checkbox" name="consent_no_late_addition" id="consent_no_late_addition" value="1" required>
                                                    <label class="form-check-label small" for="consent_no_late_addition">No author can be added after the abstract is submitted.*</label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Honeypot Bot Protection --}}
                                        <div style="display: none;">
                                            <input type="text" name="extra_info" id="extra_info" value="">
                                        </div>

                                        <div class="row pt-4 border-top">
                                            <div class="col-md-12">
                                                <div id="action_buttons_participant" style="display: {{ old('is_author') == '1' ? 'none' : 'block' }};">
                                                    @if(($settings['is_payment_enabled'] ?? 'true') == 'true')
                                                        <button type="submit" class="btn btn-primary" name="action" value="save-pay">
                                                            <i class="fa fa-credit-card"></i> Save & Continue to Payment
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-primary" name="action" value="save-close">
                                                            <i class="fa fa-user-plus"></i> Complete Registration
                                                        </button>
                                                    @endif
                                                </div>
                                                <div id="action_buttons_author" style="display: {{ old('is_author') == '1' ? 'block' : 'none' }};">
                                                    <button type="submit" class="btn btn-success" name="action" value="save-close">
                                                        <i class="fa fa-paper-plane"></i> Submit Abstract
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row"><h1 class="text-center">Registration is full.</h1></div>
                    @endif

                @else
                    <div class="row">
                        <h1 class="text-center">The event registration has closed on {{ $eventCloseDate->format('Y-m-d H:i:s') }} </h1>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <!-- Co-author Template -->
    <template id="co_author_template">
        <div class="co-author-entry border p-3 mb-3 rounded position-relative">
            <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 10px; right: 10px;" onclick="removeCoAuthor(this)">×</button>
            <h6 class="mb-3">Co-Author #<span class="author-index"></span></h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="co_authors[{index}][name]" class="form-control form-control-sm" placeholder="Full Name*" required>
                </div>
                <div class="col-md-6 mb-2">
                    <input type="email" name="co_authors[{index}][email]" class="form-control form-control-sm" placeholder="Email Address*" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="co_authors[{index}][designation]" class="form-control form-control-sm" placeholder="Designation*" required>
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" name="co_authors[{index}][department]" class="form-control form-control-sm" placeholder="Department">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="co_authors[{index}][institution]" class="form-control form-control-sm" placeholder="Institution*" required>
                </div>
                <div class="col-md-6 mb-2">
                    <select name="co_authors[{index}][country_id]" class="form-control form-control-sm" required>
                        <option value="">Select Country*</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input presenting-author-radio" type="radio" name="presenting_author_index" value="{index}">
                <label class="form-check-label small">Presenting Author</label>
            </div>
        </div>
    </template>

@endsection

@push('script')
    <script>
        let coAuthorIndex = {{ old('co_authors') ? count(old('co_authors')) : 0 }};
        const tracks = @json($tracks);

        function countWords() {
            const textarea = document.getElementById('abstract_text');
            if (!textarea) return;
            const counter = document.getElementById('word_count');
            const display = document.getElementById('word_count_display');
            
            const text = textarea.value.trim();
            const words = text ? text.split(/\s+/).length : 0;
            
            counter.innerText = words;
            
            if (words > 300) {
                display.classList.remove('text-muted');
                display.classList.add('text-danger', 'font-weight-bold');
            } else {
                display.classList.remove('text-danger', 'font-weight-bold');
                display.classList.add('text-muted');
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

        // Initialize counts on load
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('abstract_text')) countWords();
            if (document.getElementById('keywords')) countKeywords('keywords', 'keyword_count');
        });

        function updateSubTracks() {
            const trackId = document.getElementById('track_id').value;
            const subTrackSelect = document.getElementById('sub_track_id');
            const oldSubTrackId = "{{ old('sub_track_id') }}";

            subTrackSelect.innerHTML = '<option value="">Select Sub-Theme</option>';

            if (trackId) {
                const selectedTrack = tracks.find(t => t.id == trackId);
                if (selectedTrack && selectedTrack.sub_tracks) {
                    selectedTrack.sub_tracks.forEach(sub => {
                        const option = document.createElement('option');
                        option.value = sub.id;
                        option.text = sub.name;
                        if (sub.id == oldSubTrackId) {
                            option.selected = true;
                        }
                        subTrackSelect.add(option);
                    });
                }
            }
        }

        function toggleAbstractSection() {
            const isAuthor = document.getElementById('submit_abstract').checked;
            const isSubmissionOpen = {{ ($settings['is_abstract_submission_open'] ?? 'true') == 'true' ? 'true' : 'false' }};
            const showForm = isAuthor && isSubmissionOpen;

            const abstractSection = document.getElementById('abstract_section');
            if (abstractSection) {
                abstractSection.style.display = showForm ? 'block' : 'none';

                // Toggle required attributes for ALL required fields within abstract section
                const fields = abstractSection.querySelectorAll('[data-required="true"], input[required], textarea[required], select[required]');
                fields.forEach(el => {
                    if (showForm) {
                        el.setAttribute('required', 'required');
                    } else {
                        el.removeAttribute('required');
                    }
                });
            }

            const authorButtons = document.getElementById('action_buttons_author');
            const participantButtons = document.getElementById('action_buttons_participant');

            if (authorButtons) authorButtons.style.display = showForm ? 'block' : 'none';
            if (participantButtons) participantButtons.style.display = showForm ? 'none' : 'block';

            // If it's Author but submission is closed, show a "Register" button instead of "Submit Abstract"
            @if(($settings['is_abstract_submission_open'] ?? 'true') == 'false')
                if (participantButtons) {
                    const btn = participantButtons.querySelector('button');
                        if (isAuthor) {
                            btn.innerHTML = '<i class="fa fa-user-plus"></i> Register as Author';
                            btn.value = 'save-close';
                        } else {
                            if ({{ ($settings['is_payment_enabled'] ?? 'true') == 'true' ? 'true' : 'false' }}) {
                                btn.innerHTML = '<i class="fa fa-credit-card"></i> Save & Continue to Payment';
                                btn.value = 'save-pay';
                            } else {
                                btn.innerHTML = '<i class="fa fa-user-plus"></i> Complete Registration';
                                btn.value = 'save-close';
                            }
                        }
                }
            @endif
        }

        function addCoAuthor() {
            const container = document.getElementById('co_authors_container');
            const template = document.getElementById('co_author_template').innerHTML;
            const html = template.replace(/{index}/g, coAuthorIndex);

            const div = document.createElement('div');
            div.innerHTML = html;
            container.appendChild(div.firstElementChild);

            updateAuthorIndices();
            coAuthorIndex++;
        }

        function removeCoAuthor(btn) {
            btn.closest('.co-author-entry').remove();
            updateAuthorIndices();
        }

        function updateAuthorIndices() {
            const entries = document.querySelectorAll('.co-author-entry');
            entries.forEach((entry, idx) => {
                entry.querySelector('.author-index').innerText = idx + 1;
            });
        }

        // Initialize co-authors if we have old data (validation failed)
        @if(old('co_authors'))
            @foreach(old('co_authors') as $idx => $author)
                addCoAuthor();
                // We'd ideally populate the fields here, but for simplicity we just add the rows.
                // In a real app, you'd populate values or use Blade to render initial co-authors.
            @endforeach
        @endif


        document.addEventListener('DOMContentLoaded', () => {
            toggleAbstractSection();
            updateSubTracks();
        });
    </script>
@endpush

@push('style')
    <style>
        .bg-color, .bg-color-form {
            background: #ffffff;
            padding: 30px;
            height: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .bg-color-form {
            border-top: 5px solid #007bff;
        }
        .title-section {
            padding: 60px 0 30px;
            color: white;
            margin-bottom: 40px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #007bff;
        }
        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        .fee-information {
            font-size: 15px;
            line-height: 1.8;
        }
        .fee-table {
            background: #fff;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .fee-table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #007bff;
            color: #333;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 10px;
        }
        .fee-table td {
            padding: 10px;
            vertical-align: middle;
            border-top: 1px solid #eee;
        }
        .fee-table tbody tr:hover {
            background-color: #f0f7ff;
        }


        /*/*/
        .main-title {
            display: block;
            font-size: 24px;
            color: #000000;
            font-weight: 700;
            line-height: normal;
            font-family: 'edo', sans-serif;
        }
        .second-title {
            display: block;
            font-family: 'edo', sans-serif;
            font-size: 20px;
            color: black;
            line-height: normal;
            margin: 10px 0;


        }
        .sub-title {
            display: block;
            font-family: 'GlacialIndifference-Regular', sans-serif;
            font-size: 18px;
            color: #000000;
            line-height: normal;
            font-weight: bold;
            padding-bottom: 35px;
        }

        @font-face {
            font-family: 'edo';
            src: url('{{"fonts/edo.ttf"}}') format('truetype');
        }
        @font-face {
            font-family: 'GlacialIndifference-Regular';
            src: url('{{"fonts/GlacialIndifference-Regular.otf"}}') format('truetype');
        }






    </style>


@endpush
