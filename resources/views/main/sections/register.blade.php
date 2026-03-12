<section id="registration" class="wow fadeInUp">

    <div class="container">

        <div class="section-header">
            <h2>Registration </h2>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="name" class="col-md-12 col-form-label text-md-end">{{ __('Name') }}</label>
                </div>
                <div class="col-md-9">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="email" class="col-md-12 col-form-label text-md-end">{{ __('E-Mail Address') }}</label>
                </div>
                <div class="col-md-9">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="password" class="col-md-12 col-form-label text-md-end">{{ __('Password') }}</label>
                </div>
                <div class="col-md-9">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="password-confirm" class="col-md-12 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                </div>
                <div class="col-md-9">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>
            <div class="row form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                <div class="col-md-3">
                    <label for="phone">Phone Number*</label>
                </div>
                <div class="col-md-9">
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', isset($profile) ? $profile->name : '') }}" required>
                    @if($errors->has('phone'))
                        <p class="help-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row form-group {{ $errors->has('institute_name') ? 'has-error' : '' }}">
                <div class="col-md-3">
                    <label for="description">Institute Name *</label>
                </div>
                <div class="col-md-9">
                    <input type="text" id="institute_name" name="institute_name" class="form-control" value="{{ old('institute_name', isset($profile) ? $profile->institute_name : '') }}" required>
                    @if($errors->has('institute_name'))
                        <p class="help-block">
                            {{ $errors->first('institute_name') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row form-group {{ $errors->has('academic_major') ? 'has-error' : '' }}">
                <div class="col-md-3">
                    <label for="full_description">Academic Major *</label>
                </div>
                <div class="col-md-9">
                    <input type="text" id="academic_major" name="academic_major" class="form-control" value="{{ old('academic_major', isset($profile) ? $profile->academic_major : '') }}" required>
                    @if($errors->has('full_description'))
                        <p class="help-block">
                            {{ $errors->first('academic_major') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row form-group {{ $errors->has('part_aws_cloud_club') ? 'has-error' : '' }}">
                <div class="col-md-3">
                    <label for="part_aws_cloud_club">Are you part of an AWS Cloud Club? *</label>
                </div>
                <div class="col-md-9">
                    <input type="radio" name="part_aws_cloud_club" value="AWS Cloud Club at BRAC University"> AWS Cloud Club at BRAC University <br/>
                    <input type="radio" name="part_aws_cloud_club" value="AWS Cloud Club at KUET"> AWS Cloud Club at KUET <br/>
                    <input type="radio" name="part_aws_cloud_club" value="Other"> Other
                    @if($errors->has('part_aws_cloud_club'))
                        <p class="help-block">
                            {{ $errors->first('part_aws_cloud_club') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row form-group {{ $errors->has('tracks_like') ? 'has-error' : '' }}">
                <div class="col-md-3">
                    <label for="tracks_like">Are you part of an AWS Cloud Club? *</label>
                </div>
                <div class="col-md-9">
                    <input type="radio" name="tracks_like" value="Technical Track"><a href="#" data-toggle="tooltip" data-placement="top" title="Hands on workshop on building cloud native applications and monetizing using data driven machine learning."> Technical Track</a> <br/>
                    <input type="radio" name="tracks_like" value="Business Track"> <a href="#" data-toggle="tooltip" data-placement="top" title="Hands on workshop on building the next generation billion dollar unicorn and AWS partner program to extend your customer reach.">Business Track</a> <br/>
                    <input type="radio" name="tracks_like" value="Career Track"> <a href="#" data-toggle="tooltip" data-placement="top" title="Educators will learn about AWS student programs, and specific incentives for institutions.">Career Track</a>
                    @if($errors->has('tracks_like'))
                        <p class="help-block">
                            {{ $errors->first('tracks_like') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row form-group {{ $errors->has('tracks_like') ? 'has-error' : '' }}">
                <div class="col-md-3">
                    <label for="tracks_like">How familiar are you with AWS products and services? *</label>
                </div>
                <div class="col-md-9">
                    <input type="radio" name="aws_familiar" value="I have an AWS account but have not built anything"> I have an AWS account but have not built anything <br/>
                    <input type="radio" name="aws_familiar" value="I have used AWS to build at least one deliverable"> I have used AWS to build at least one deliverable <br/>
                    <input type="radio" name="aws_familiar" value="I have used AWS more than once to build something"> I have used AWS more than once to build something <br/>
                    <input type="radio" name="aws_familiar" value="I have never used AWS but am interested in learning"> I have never used AWS but am interested in learning <br/>
                    <input type="radio" name="aws_familiar" value="Other"> Other
                    @if($errors->has('tracks_like'))
                        <p class="help-block">
                            {{ $errors->first('tracks_like') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row form-group {{ $errors->has('comments') ? 'has-error' : '' }}">
                <div class="col-md-3">
                    <label for="description">Comments</label>
                </div>
                <div class="col-md-9">
                    <textarea id="comments" name="comments" class="form-control ">{{ old('comments', isset($profile) ? $profile->comments : '') }}</textarea>
                    @if($errors->has('comments'))
                        <p class="help-block">
                            {{ $errors->first('comments') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row form-group {{ $errors->has('coupon') ? 'has-error' : '' }}">
                <div class="col-md-3">
                    <label for="phone">Coupon Code if any</label>
                </div>
                <div class="col-md-9">
                    <input type="text" id="coupon_code" name="coupon" class="form-control" value="{{ old('coupon', isset($profile) ? $profile->coupon : '') }}">
                    @if($errors->has('phone'))
                        <p class="help-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                    <h3 id="coupon_validation_message"></h3>
                </div>
                
            </div>
            <div class="row mb-0">
                <div class="col-md-3"></div>
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Register') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
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
                    } else {
                        $('#coupon_validation_message').text('Invalid coupon code or email.');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', error);
                }
            });
        });
    });
</script>
