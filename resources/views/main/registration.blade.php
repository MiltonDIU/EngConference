@extends('layouts.main')

@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section" style="background: linear-gradient(to bottom, black, black)">
                <div class="container">
                    <div class="section-header">
                        <h3>Register Now</h3>
                        <p>Registration Form</p>
                    </div>
                </div>
            </div>
            <div class="container">
                @if(session()->has('message'))
                    <div class="alert alert-success alert-dismissible">
                        <strong>Success!</strong> {{ session()->get('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @php
                    $eventStartDate = \Carbon\Carbon::parse($settings['registration_start_date']);
                    $eventCloseDate = \Carbon\Carbon::parse($settings['registration_close_date']);
                    $eventEarlyRegDate = \Carbon\Carbon::parse($settings['early_registration_last_date']);
                    $currentDate = \Carbon\Carbon::now();
                @endphp

                @if ($currentDate < $eventStartDate)
                    <div class="row">
                        <h1 class="text-center">The event registration has not started yet. It will start on {{ $eventStartDate->format('Y-m-d H:i:s') }}</h1>
                    </div>
                @elseif ($currentDate >= $eventStartDate && $currentDate <= $eventCloseDate)

                    @if($settings['seat_is_full']=='false')
                        <div class="row">
                            <div class="col-md-6 line">
                                <div class="bg-color">
                                    <h4><strong> {{ $settings['title'] }}</strong></h4>
                                    <div><img width="20px;" src="{{ asset('/') }}img/calendar.png"> {{ $settings['about_when'] }}</div>
                                    <div><img width="20px;" src="{{ asset('/') }}img/clock.png"> 08: 30 AM - 06:00 PM</div>
                                    <div style="color:#000000;"><img width="20px;" src="{{ asset('/') }}img/location.png">Location: <a href="https://goo.gl/maps/YLzvMSHBVr1GQCFdA" target="_blank"> {{ $settings['about_where'] }} </a></div>
                                    <br/>
                                    <div class="fee-information">
                                        <div><strong>Registration Fee : {{ $settings['event_price'] }} BDT</strong></div>
                                        <div><strong>Early Registration Fee : {{ $settings['early_registration_event_price'] }} BDT</strong></div>
                                        {{--                                        <div><strong>For Daffodil Students :400 BDT</strong> <small><em>(use Student Email)</em></small> </div>--}}
                                        <div><strong>Reg. Starting: {{ $eventStartDate->formatLocalized('%e %B %Y %H:%M:%S') }} </strong></div>
                                        <div><strong>Reg. Early Deadline: {{ $eventEarlyRegDate->formatLocalized('%e %B %Y %H:%M:%S') }} </strong></div>
                                        <div><strong>Reg. Deadline: {{ $eventCloseDate->formatLocalized('%e %B %Y %H:%M:%S') }} </strong></div>
                                    </div>
                                    <br/>

                                    <h5><strong>Participation Benefits</strong> </h5>
                                    <ul>
                                        @foreach($aminities as $aminity)
                                            <li>{{ $aminity->name }}</li>
                                        @endforeach
                                    </ul>


                                    <div><strong>About Event</strong></div>

                                    <br>
                                    <div style="text-align:justify;">
                                        {!! $settings['about_description'] !!}
                                    </div>
                                    <br>
                                    {{--                                    <p><strong>Why will you join us?</strong></p>--}}
                                    {{--                                    <ul>--}}
                                    {{--                                        <li>Interacting with AWS representatives!</li>--}}
                                    {{--                                        <li>Unleashing the Power of Cloud Innovation!</li>--}}
                                    {{--                                        <li>Discover Next-Gen Cloud Solutions!</li>--}}
                                    {{--                                        <li>Hands-on Experience!</li>--}}
                                    {{--                                        <li>Network with Industry Experts!</li>--}}
                                    {{--                                        <li>Learn from Real-World Use Cases!</li>--}}
                                    {{--                                        <li>Embrace the Cloud Revolution!</li>--}}
                                    {{--                                        <li>AWS Certifications!</li>--}}
                                    {{--                                    </ul>--}}
                                </div>
                            </div>

                            <div class="col-md-6 line">
                                <div class="bg-color">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="name"><strong>{{ __('Full Name') }}</strong></label>
                                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                                @error('name')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="email"><strong>{{ __('E-Mail Address') }}</strong></label>

                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                                @error('email')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="password"><strong>{{ __('Password') }}</strong></label>

                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                                @error('password')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="password-confirm"><strong>{{ __('Confirm Password') }}</strong></label>

                                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                            </div>
                                        </div>
                                        <div class="row form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                            <div class="col-md-12">
                                                <label for="phone"><strong>Phone Number*</strong></label>
                                                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', isset($profile) ? $profile->name : '') }}" required>
                                                @if($errors->has('phone'))
                                                    <p class="help-block">
                                                        {{ $errors->first('phone') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row form-group {{ $errors->has('institute_name') ? 'has-error' : '' }}">
                                            <div class="col-md-12">
                                                <label for="description"><strong>Institution Name *</strong></label>

                                                <input type="text" id="institute_name" name="institute_name" class="form-control" value="{{ old('institute_name', isset($profile) ? $profile->institute_name : '') }}" required>
                                                @if($errors->has('institute_name'))
                                                    <p class="help-block">
                                                        {{ $errors->first('institute_name') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>



                                        <div class="row form-group {{ $errors->has('schedule_ids') ? 'has-error' : '' }}">
                                            <div class="col-md-12">
                                                <label for="part_aws_cloud_club"><strong>Select Workshop(s) *</strong></label>
                                                <br>
                                                <strong>For each time slot, please select only a single workshop. <span id="maximum_select_schedule"></span></strong>
                                                <br>
                                                Thanks for your cooperation!
                                                <hr>
                                                {{--                                                @foreach($schedules as $key=> $schedule)--}}
                                                {{--                                                <span>{{ "Day - ".$key}}</span><br>--}}
                                                {{--                                                    @foreach($schedule as $key=> $sc)--}}
                                                {{--                                                        <input type="checkbox" name="schedule_ids[]" value="{{$sc->id }}"--}}
                                                {{--                                                            {{ in_array($sc->id, old('schedule_ids', [])) ? 'checked' : '' }}>--}}
                                                {{--                                                        {{ $sc->title }}   <br>  <strong><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($sc->start_time)->format('h:i A') }}</strong>--}}
                                                {{--                                                        -- Available Seats: {{ $sc->total_seat - $sc->users->count() }}--}}
                                                {{--                                                        <br>--}}
                                                {{--                                                    @endforeach--}}
                                                {{--                                                    <hr>--}}
                                                {{--                                                @endforeach--}}


                                                @foreach ($schedules as $dayKey => $daySchedules)
                                                    <span style="font-size: 22px; font-weight: bold">{{ "Day - " . $dayKey }}</span>

                                                    @php
                                                        $timeis = "";
                                                    @endphp
                                                    @foreach ($daySchedules as $index=> $schedule)
                                                        @if($index+1<count($daySchedules))
                                                            @if($timeis != $schedule->start_time)
                                                                <div style="background: rebeccapurple; height: 1px; width: 100%; margin: 15px 0px">

                                                                </div>
                                                                <strong>Please select one from each time slot</strong>
                                                                <br>
                                                            @endif
                                                        @endif
                                                        <input
                                                            type="checkbox"
                                                            class="schedule-checkbox {{ $dayKey }}"
                                                            data-start-time="{{ $schedule->start_time }}"
                                                            data-day="{{ $dayKey }}"
                                                            name="schedule_ids[]"
                                                            value="{{ $schedule->id }}"
                                                            {{ in_array($schedule->id, old('schedule_ids', [])) ? 'checked' : '' }}
                                                        >
                                                        {{ $schedule->title }}
                                                        <br>
                                                        <strong><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</strong>
{{--                                                        -- Available Seats: {{ $schedule->total_seat - $schedule->users->count() }}--}}





                                                        @if($index+1<count($daySchedules))

                                                            @if($timeis == $schedule->start_time)
                                                              <br>
                                                                @php
                                                                    $timeis = "";
                                                                @endphp
                                                                @else
                                                                    <br>
                                                                    <br>
                                                            @endif
                                                        @endif



                                                        @php
                                                            $timeis = $schedule->start_time;
                                                        @endphp

                                                    @endforeach
                                                    @if($dayKey<count($schedules))
                                                        <div style="background: rebeccapurple; height: 3px; width: 100%; margin: 15px 0px"></div>
                                                    @endif
                                                @endforeach



                                                @if($errors->has('schedule_ids'))
                                                    <p class="text-danger">{{ $errors->first('schedule_ids') }}</p>
                                                @endif

                                            </div>
                                        </div>
                                        <br>
                                        <div class="row form-group">
                                            <div class="col-12">
                                                <label for="radioButton"><strong>Any Coupon</strong></label>
                                                <input type="radio" name="radioButton" id="radioYes" value="yes" onclick="toggleTextField()"> Yes
                                                <input type="radio" name="radioButton" id="radioNo" value="no" checked onclick="toggleTextField()"> No
                                            </div>
                                        </div>
                                        <div class="row form-group {{ $errors->has('coupon') ? 'has-error' : '' }}"  id="textFieldContainer" style="display:none;">
                                            <div class="col-md-12">
                                                <label for="phone"><strong>Coupon Code</strong></label>
                                                <input type="text" id="coupon_code" name="coupon" class="form-control" value="{{ old('coupon', isset($profile) ? $profile->coupon : '') }}">
                                                @if($errors->has('phone'))
                                                    <p class="help-block">
                                                        {{ $errors->first('phone') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <h4 id="coupon_validation_message"></h4>
                                        </div>

                                        <div class="row mb-0">
                                            <div class="col-md-12" id="coupon">
                                                <div id="message"></div>

                                                <button type="submit" class="btn btn-info btn-sm" name="action" value="save-close" id="save-close">
                                                    <i class="fa fa-dot-circle-o"></i> Save & Close
                                                </button>
                                                <button type="submit" class="btn btn-primary btn-sm" name="action" value="save-pay" id="save-pay">
                                                    <i class="fa fa-dot-circle-o"></i> Save & Pay
                                                </button>



                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>


                        </div>
                    @else
                        <div class="row">
                            <h1 class="text-center">We are working on this site, please check after 1 hour</h1>
                        </div>
                    @endif

                @else
                    <div class="row">
                        <h1 class="text-center">The event registration has closed. It closed on {{ $eventCloseDate->format('Y-m-d H:i:s') }}</h1>
                    </div>
                @endif

            </div>
        </section>
    </main>
@endsection
@push('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkboxes = document.querySelectorAll(".schedule-checkbox");

            // Function to update button state and message based on the checkboxes
            function updateButtonStateAndMessage() {
                const timeSlots = new Map();

                checkboxes.forEach(function (checkbox) {
                    const day = checkbox.getAttribute("data-day");
                    const startTime = checkbox.getAttribute("data-start-time");

                    if (!timeSlots.has(day)) {
                        timeSlots.set(day, new Map());
                    }

                    const timeSlotMap = timeSlots.get(day);

                    if (!timeSlotMap.has(startTime)) {
                        timeSlotMap.set(startTime, []);
                    }

                    timeSlotMap.get(startTime).push(checkbox);
                });

                // Enable or disable buttons based on the number of selected schedules for each time slot
                const saveCloseButton = document.getElementById("save-close");
                const savePayButton = document.getElementById("save-pay");

                let allSchedulesSelected = true;

                timeSlots.forEach(function (timeSlotMap) {
                    timeSlotMap.forEach(function (checkboxesInSlot) {
                        const checkedCheckboxesInSlot = checkboxesInSlot.filter(checkbox =>
                            checkbox.checked
                        );

                        // Disable other checkboxes in the same time slot if at least one is checked
                        checkboxesInSlot.forEach(function (otherCheckbox) {
                            if (otherCheckbox !== checkedCheckboxesInSlot[0]) {
                                otherCheckbox.disabled = checkedCheckboxesInSlot.length > 0;
                            }

                            // Update the overall state based on whether at least one schedule is selected in each time slot
                            if (!otherCheckbox.disabled && !otherCheckbox.checked) {
                                allSchedulesSelected = false;
                            }
                        });
                    });
                });

                // Enable or disable buttons based on the overall state
                saveCloseButton.disabled = !allSchedulesSelected;
                savePayButton.disabled = !allSchedulesSelected;

                // Count the total number of unique time slots
                const uniqueTimeSlotsCount = [...timeSlots.values()].reduce((count, timeSlotMap) => count + timeSlotMap.size, 0);

                // Update the message div and set text color class
                const messageElement = document.getElementById("message");
                if (allSchedulesSelected) {
                    messageElement.innerText = `Your form is ready to submit!`;
                    messageElement.classList.remove("error");
                    messageElement.classList.add("ready");
                } else {
                    messageElement.innerText = `Please select at least one schedule for each time slot.`;
                    messageElement.classList.remove("ready");
                    messageElement.classList.add("error");
                }

                // Update the total time slots inside the element with ID 'maximum_select_schedule'
                const maximumSelectScheduleElement = document.getElementById("maximum_select_schedule");
                maximumSelectScheduleElement.innerText = `Individual participants should take up to ${uniqueTimeSlotsCount} workshops.`;
            }

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener("change", function () {
                    // Update the button state and message whenever a checkbox is changed
                    updateButtonStateAndMessage();
                });
            });

            // Check the initial state of checkboxes and update the message on page load
            updateButtonStateAndMessage();
        });
    </script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
        /*-----------discunt calculation -------------*/
        $(document).ready(function() {
            $('#coupon_code').on('blur', function() {
                var couponCode = $(this).val();
                var email = $('#email').val();
                var validCoupon = document.getElementById('validCoupon');
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                console.log(email);
                $.ajax({
                    type: 'POST',
                    url: "{{ 'validate-coupon' }}",
                    data: {
                        coupon_code: couponCode,
                        email: email,
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.valid) {
                            $('#coupon_validation_message').text(response.message);
                            validCoupon.remove();
                        } else {
                            $('#coupon_validation_message').text(response.message);
                            if ($('#validCoupon').length === 0) {
                                var couponElement = document.getElementById('coupon');
                                var savePayButton = document.createElement('button');
                                savePayButton.id = 'validCoupon';
                                savePayButton.type = 'submit';
                                savePayButton.className = 'btn btn-primary btn-sm';
                                savePayButton.name = 'action';
                                savePayButton.value = 'save-pay';
                                savePayButton.innerHTML = '<i class="fa fa-dot-circle-o"></i> Save & Pay';
                                couponElement.appendChild(savePayButton);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX error:', error);
                    }
                });
            });
        });

        function toggleTextField() {
            var textFieldContainer = document.getElementById("textFieldContainer");
            var radioYes = document.getElementById("radioYes");

            if (radioYes.checked) {
                textFieldContainer.style.display = "block";
            } else {
                textFieldContainer.style.display = "none";
            }
        }
        function toggleInputFields(radio) {
            var inputFields = document.getElementById("inputFields");

            if (radio.value === "yes") {
                inputFields.style.display = "block";
                inputField1.setAttribute("required", "required");
                inputField2.setAttribute("required", "required");
            } else {
                inputFields.style.display = "none";
                inputField1.removeAttribute("required");
                inputField2.removeAttribute("required");
            }
        }
    </script>

    <!-- Add this script after your existing scripts -->
    <script>
        function getReferralCouponId(url) {
            // Extract the last part of the URL
            var urlParts = url.split('/');
            var referralId = urlParts[urlParts.length - 1];

            // Check if the last part is a valid referral ID (you may need to adjust this condition)
                // Assume you have an AJAX endpoint to retrieve the coupon ID based on the referral ID
                // Adjust the URL and data as needed
            if(referralId!='book-ticket'){
                $.ajax({
                    type: 'GET',
                    url: '/check-referral-coupon',
                    data: { referral_id: referralId },
                    async: false, // Synchronous request to get the coupon ID
                    success: function(response) {
                        couponId = response.coupon_id;
                    },
                    error: function(xhr, status, error) {
                        //console.log('AJAX error:', error);
                    }
                });
                return couponId;
            }
        }
        $(document).ready(function() {

            var url = window.location.href;
            var referralCouponId = getReferralCouponId(url);
            if (referralCouponId) {
                // Set the radio button to "Yes"
                $('#radioYes').prop('checked', true);
                // Display the input field
                $('#textFieldContainer').show();
                // Populate the coupon code input field
                $('#coupon_code').val(referralCouponId);

            }
        });
    </script>




@endpush





@push('style')
    <style>
        .bg-color {
            background: #fbf8f8;
            padding: 20px;
            height: 100%;
        }
        .title-section {
            padding-top: 42px;
            background: gainsboro;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        input[type="checkbox"]{
            width: 18px;
            height: 18px;
        }
        #message {
            color: black; /* Default color */
        }

        #message.ready {
            color: green;
        }

        #message.error {
            color: red;
        }
    </style>
@endpush

