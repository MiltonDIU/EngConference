@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">

        </div>
        <div class="card-body">
            <form action="{{ route("update-profile") }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="phone">Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($user) ? $user->name : '') }}" disabled>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="phone">Email</label>
                    <input type="text" id="email" name="email" class="form-control" value="{{ old('email', isset($user) ? $user->email : '') }}" disabled>
                    @if($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                    <label for="phone">Phone Number*</label>
                    <input type="hidden" name="id" value="{{ $profile->id }}">
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', isset($profile) ? $profile->phone : '') }}" required>
                    @if($errors->has('phone'))
                        <p class="help-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('institute_name') ? 'has-error' : '' }}">
                    <label for="description">Institute Nane *</label>
                    <input type="text" id="institute_name" name="institute_name" class="form-control" value="{{ old('institute_name', isset($profile) ? $profile->institute_name : '') }}" required>
                    @if($errors->has('institute_name'))
                        <p class="help-block">
                            {{ $errors->first('institute_name') }}
                        </p>
                    @endif
                </div>

                <div class="row form-group" >
                    <div class="col-md-12">
                        <label for="phone"><strong>Coupon Code</strong></label>
                        <input type="text" id="coupon_code" name="coupon_code" class="form-control" value="{{ old('coupon_code', isset($profile) ? $profile->coupon_code : '') }}">
                        @if($errors->has('coupon_code'))
                            <p class="help-block">
                                {{ $errors->first('coupon_code') }}
                            </p>
                        @endif
                    </div>
                    <h4 id="coupon_validation_message"></h4>
                </div>
                <div class="form-group {{ $errors->has('payment_status') ? 'has-error' : '' }}">
                    <label for="payment_status"><strong>Payment Status</strong></label>
                    <select name="payment_status" class="form-control">
                        <option value=""> ========= Select One =========== </option>
                        <option value="0" {{ $profile->payment_status == 0?'selected':'' }}> Payment Not Complete</option>
                        <option value="1" {{ $profile->payment_status == 1?'selected':'' }}> Payment Complete.</option>
                    </select>
                    @if($errors->has('payment_status'))
                        <p class="help-block">
                            {{ $errors->first('payment_status') }}
                        </p>
                    @endif
                </div>





{{--                value="{{ $schedule->id }}"--}}
{{--                {{ in_array($schedule->id, $registration->schedule_ids) --}}
{{--            --}}

            <div class="form-group {{ $errors->has('pay_amount') ? 'has-error' : '' }}">
                    <label for="description">Pay Amount *</label>
                    <input type="number" id="pay_amount" name="pay_amount" class="form-control" value="{{ old('pay_amount', isset($profile) ? $profile->pay_amount : '') }}" required>
                    @if($errors->has('pay_amount'))
                        <p class="help-block">
                            {{ $errors->first('pay_amount') }}
                        </p>
                    @endif
                </div>

                <div class="row form-group {{ $errors->has('schedule_ids') ? 'has-error' : '' }}">
                    <div class="col-md-12">
                        <label for="part_aws_cloud_club"><strong>Select Workshop(s) *</strong></label>
                    </div>

                    @foreach ($schedules as $dayKey => $daySchedules)
                        <div class="col-md-4">

                            <span>{{ "Day - " . $dayKey }}</span>
                            <br>
                            @foreach ($daySchedules as $schedule)
                                <input
                                    type="checkbox"
                                    class="schedule-checkbox {{ $dayKey }}"
                                    data-start-time="{{ $schedule->start_time }}"
                                    data-day="{{ $dayKey }}"
                                    name="schedule_ids[]"
                                    value="{{ $schedule->id }}"
                                    {{ in_array($schedule->id, $workshops) ? 'checked' : '' }}
                                >
                                {{ $schedule->title }}
                                <br>
                                <strong><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</strong>
                                -- Available Seats: {{ $schedule->total_seat - $schedule->users->count() }}
                                <br>
                                <br>
                            @endforeach
                        </div>
                    @endforeach
                    @if($errors->has('schedule_ids'))
                        <p class="text-danger">{{ $errors->first('schedule_ids') }}</p>
                    @endif


                </div>
                <div>
                    <input class="btn btn-primary" type="submit" value="{{ trans('global.update') }}">
                </div>
            </form>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    if (response.valid) {
                        $('#coupon_validation_message').text('Coupon code is valid.');
                        validCoupon.remove();
                    } else {
                        $('#coupon_validation_message').text('Invalid coupon code or email.');
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
</script>
<style>
    .bg-color {
        background: #fbf8f8;
        padding: 20px;
        height: 100%;
    }
</style>
