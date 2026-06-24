@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">

        </div>
        <div class="card-body">
            <form action="{{ route("update-profile") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $profile->id }}">

                <!-- Section 1: Personal Information -->
                <h5 class="text-primary mb-3 border-bottom pb-2"><i class="fas fa-user-circle mr-2"></i> Personal Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name', $profile->first_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name', $profile->last_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email (Read-only)</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="whatsapp_number">WhatsApp Number *</label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $profile->whatsapp_number) }}" required>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Professional Information -->
                <h5 class="text-primary mt-4 mb-3 border-bottom pb-2"><i class="fas fa-briefcase mr-2"></i> Professional Information</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="designation">Designation *</label>
                            <input type="text" id="designation" name="designation" class="form-control" value="{{ old('designation', $profile->designation) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" id="department" name="department" class="form-control" value="{{ old('department', $profile->department) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="institution">Institution *</label>
                            <input type="text" id="institution" name="institution" class="form-control" value="{{ old('institution', $profile->institution) }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="country_id">Country *</label>
                            <select name="country_id" id="country_id" class="form-control select2" required>
                                <option value="">--- Select Country ---</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ $profile->country_id == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Registration & Payment -->
                <h5 class="text-primary mt-4 mb-3 border-bottom pb-2"><i class="fas fa-file-invoice-dollar mr-2"></i> Registration & Payment</h5>
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="registration_id">Registration ID</label>
                            <input type="text" id="registration_id" name="registration_id" class="form-control" value="{{ old('registration_id', $profile->registration_id) }}">
                            <small class="text-muted">Usually generated automatically upon payment.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="participation_mode">Participation Mode *</label>
                            <select name="participation_mode" id="participation_mode" class="form-control" required>
                                <option value="onsite" {{ $profile->participation_mode == 'onsite' ? 'selected' : '' }}>Onsite</option>
                                <option value="online" {{ $profile->participation_mode == 'online' ? 'selected' : '' }}>Online</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group py-4">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_author" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_author" name="is_author" value="1" {{ $profile->is_author ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="is_author">Registered as Author?</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group py-4">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="author_list_confirmed" value="0">
                                <input type="checkbox" class="custom-control-input" id="author_list_confirmed" name="author_list_confirmed" value="1" {{ $profile->author_list_confirmed ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="author_list_confirmed">Author List Confirmed?</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="pay_amount">Pay Amount *</label>
                            <div class="input-group">
                                <input type="number" step="0.01" id="pay_amount" name="pay_amount" class="form-control" value="{{ old('pay_amount', $profile->pay_amount) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <input type="text" id="currency" name="currency" class="form-control" value="{{ old('currency', $profile->currency) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="coupon_code">Coupon Code</label>
                            <input type="text" id="coupon_code" name="coupon_code" class="form-control" value="{{ old('coupon_code', $profile->coupon_code) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_status">Payment Status *</label>
                            <select name="payment_status" id="payment_status" class="form-control" required>
                                <option value="0" {{ $profile->payment_status == '0' ? 'selected' : '' }}>Not Complete</option>
                                <option value="1" {{ $profile->payment_status == '1' ? 'selected' : '' }}>Complete</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Workshops -->
                <h5 class="text-primary mt-4 mb-3 border-bottom pb-2"><i class="fas fa-laptop-code mr-2"></i> Workshops / Selected Sessions</h5>
                <div class="row">
                    @foreach ($schedules as $dayKey => $daySchedules)
                        <div class="col-md-4 mb-4">
                            <div class="p-3 bg-light rounded border h-100">
                                <h6 class="font-weight-bold mb-3"><i class="calendar-day mr-2"></i> {{ "Day - " . $dayKey }}</h6>
                                @foreach ($daySchedules as $schedule)
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" id="schedule_{{ $schedule->id }}" name="schedule_ids[]" value="{{ $schedule->id }}" {{ in_array($schedule->id, $workshops) ? 'checked' : '' }}>
                                        <label class="custom-control-label small" for="schedule_{{ $schedule->id }}">
                                            <strong>{{ $schedule->title }}</strong>
                                            <br>
                                            <span class="text-muted"><i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button class="btn btn-primary px-5 shadow-sm" type="submit">
                        <i class="fas fa-save mr-1"></i> {{ trans('global.save') }}
                    </button>
                    <a href="{{ route('show-profile') }}" class="btn btn-light border ml-2">Cancel</a>
                </div>
            </form>
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
