@extends('layouts.main')

@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
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
                <div class="row">
                    <div class="col-md-6 line">
                        <div class="bg-color">
                            <h4><strong>AWS Cloud Day Bangladesh</strong></h4>
                            <div><img width="20px;" src="{{ asset('/') }}img/calendar.png"> Saturday, June 17, 2023</div>
                            <div><img width="20px;" src="{{ asset('/') }}img/clock.png"> 10:00 AM - 06:00 PM</div>
                            <div style="color:#000000;"><img width="20px;" src="{{ asset('/') }}img/location.png">Location: <a href="https://goo.gl/maps/YLzvMSHBVr1GQCFdA" target="_blank"> Daffodil Plaza 4/2 Sobhanbag, Mirpur Road,</a> Dhanmondi, Dhaka-1207</div>
                            <br/>
                            <div class="fee-information">
                                <div><strong>Registration Fee :500 BDT</strong></div>
                                <div><strong>For Daffodil Students :400 BDT</strong> <small><em>(use Student Email)</em></small> </div>
                                <div><strong>Reg. Deadline: 10th June 2023</strong></div>
                            </div>
                            <br/>
                            <div><strong>Three parallel tracks:</strong></div>
                            <ol>
                                <li>
                                    <strong>Technical Track:</strong> Hands on workshop on building cloud native applications and monetizing using data driven machine learning.
                                </li>
                                <li>
                                   <strong>Business Track:</strong> Hands on workshop on building the next generation billion dollar unicorn and AWS partner program to extend your customer reach.
                                </li>
                                <li>
                                    <strong>Career Track:</strong> Educators will learn about AWS student programs, and specific incentives for institutions.
                                </li>
                            </ol>
                            <br/>
                            <div><strong>About Event</strong></div>
                            <br>
                            <div style="text-align:justify;"><strong>Join us on June 17, 2023 at AWS Cloud Day Bangladesh 2023!</strong> <br> This full day event will feature hands-on workshops on building cloud native applications and machine learning, as well as insights on the recipe for creating the next generation of unicorn startups. You'll also have the opportunity to learn about career programs offered by AWS and network with subject matter experts, national leaders, teachers, and cloud enthusiasts from all over Bangladesh. Hurry, as seats are limited - register now!</div>
                            <br>
                            <p><strong>Why will you join us?</strong></p>
                            <ul>
                                <li>Interacting with AWS representatives!</li>
                                <li>Unleashing the Power of Cloud Innovation!</li>
                                <li>Discover Next-Gen Cloud Solutions!</li>
                                <li>Hands-on Experience!</li>
                                <li>Network with Industry Experts!</li>
                                <li>Learn from Real-World Use Cases!</li>
                                <li>Embrace the Cloud Revolution!</li>
                                <li>AWS Certifications!</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 line">
                        <div class="bg-color">
                            <form method="POST" action="{{ route('register2') }}">
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
                                <div class="row form-group {{ $errors->has('academic_major') ? 'has-error' : '' }}">
                                    <div class="col-md-12">
                                        <label for="full_description"><strong>Academic Major *</strong></label>
                                        <input type="text" id="academic_major" name="academic_major" class="form-control" value="{{ old('academic_major', isset($profile) ? $profile->academic_major : '') }}" required>
                                        @if($errors->has('full_description'))
                                            <p class="help-block">
                                                {{ $errors->first('academic_major') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row form-group {{ $errors->has('part_aws_cloud_club') ? 'has-error' : '' }}">
                                    <div class="col-md-12">
                                        <label for="part_aws_cloud_club"><strong>Are you part of an AWS Cloud Club? *</strong></label>
                                        <select name="part_aws_cloud_club" class="form-control">
                                            <option value=""> =============== Select One ============== </option>
                                            <option value="AWS Cloud Club at BRAC University"> AWS Cloud Club at BRAC University </option>>
                                            <option value="AWS Cloud Club at KUET"> AWS Cloud Club at KUET </option>>
                                            <option value="Other"> Other</option>
                                        </select>
                                        @if($errors->has('part_aws_cloud_club'))
                                            <p class="help-block">
                                                {{ $errors->first('part_aws_cloud_club') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row form-group {{ $errors->has('tracks_like') ? 'has-error' : '' }}">
                                    <div class="col-md-12">
                                        <label for="tracks_like"><strong>AWS Cloud Day Bangladesh 2023 will have three parallel tracks. Which track you would like to choose (choose one) *</strong></label>
                                        <select name="tracks_like" class="form-control">
                                            <option value=""> =============== Select One ============== </option>
                                            <option value="Technical Track"> Technical Track</option>
                                            <option value="Career Track"> Career Track</option>
                                        </select>
                                            @if($errors->has('tracks_like'))
                                            <p class="help-block">
                                                {{ $errors->first('tracks_like') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row form-group {{ $errors->has('tracks_like') ? 'has-error' : '' }}">
                                    <div class="col-md-12">
                                        <label for="tracks_like"><strong>How familiar are you with AWS products and services? *</strong></label>
                                        <select name="aws_familiar" class="form-control">
                                        <option value=""> =============== Select One ============== </option>
                                        <option value="I have an AWS account but have not built anything"> I have an AWS account but have not built anything </option>
                                        <option value="I have used AWS to build at least one deliverable"> I have used AWS to build at least one deliverable </option>
                                        <option value="I have used AWS more than once to build something"> I have used AWS more than once to build something </option>
                                        <option value="I have never used AWS but am interested in learning"> I have never used AWS but am interested in learning </option>
                                        <option value="Other"> Other</option>
                                        </select>
                                        @if($errors->has('tracks_like'))
                                            <p class="help-block">
                                                {{ $errors->first('tracks_like') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-12">
                                        <label for="radioButton"><strong>Any Coupon</strong></label>
                                        <input type="radio" name="radioButton" id="radioYes" value="yes" onclick="toggleTextField()"> Yes
                                        <input type="radio" name="radioButton" id="radioNo" value="no" onclick="toggleTextField()"> No
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
                                <div class="row form-group {{ $errors->has('comments') ? 'has-error' : '' }}">
                                    <div class="col-md-12">
                                        <label for="description"><strong>Any question, comment, or suggestion?</strong></label>

                                        <textarea id="comments" name="comments" class="form-control ">{{ old('comments', isset($profile) ? $profile->comments : '') }}</textarea>
                                        @if($errors->has('comments'))
                                            <p class="help-block">
                                                {{ $errors->first('comments') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                               <div class="row form-group">
                                    <div class="col-12">
                                        <label for="radioButton"><strong> Do you have any PRODUCTION application running in AWS?</strong> (You have a chance to win free ticket to the event, and showcase your application in the "AWS Applications from Bangladesh" banner)</label>
                                        <input type="radio" name="production_app" id="yesRadio" value="yeh" onchange="toggleInputFields(this)"> Yes  (If Yes, then "Please provide the URL for the application")<br/>
                                        <input type="radio" name="production_app" id="noRadio" value="noh" onchange="toggleInputFields(this)"> No
                                    </div>
                                </div>
                                <div class="row form-group {{ $errors->has('application_url') ? 'has-error' : '' }}" id="inputFields" style="display: none;" >
                                    <div class="col-md-12">
                                        <label for="phone"><strong>Provide the application URL</strong></label>
                                        <input type="text" id="inputField1" name="application_url" class="form-control" value="{{ old('application_url', isset($profile) ? $profile->application_url : '') }}">
                                        @if($errors->has('phone'))
                                            <p class="help-block">
                                                {{ $errors->first('phone') }}
                                            </p>
                                        @endif
                                        <label for="phone"><strong>Provided the Logo URL</strong></label>
                                        <input type="text" id="inputField2" name="logo_url" class="form-control" value="{{ old('logo_url', isset($profile) ? $profile->logo_url : '') }}">
                                    </div>
                                </div>
                                <div class="row mb-0">
                                    <div class="col-md-12" id="coupon">
                                        <button type="submit" class="btn btn-info btn-sm" name="action" value="save-close">
                                            <i class="fa fa-dot-circle-o"></i> Save & Close
                                        </button>
                                        <button id="validCoupon" type="submit" class="btn btn-primary btn-sm" name="action" value="save-pay">
                                            <i class="fa fa-dot-circle-o"></i> Save & Pay
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
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
    function toggleInputFields(radio) {
        var inputFields = document.getElementById("inputFields");

        if (radio.value === "yeh") {
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
</style>
