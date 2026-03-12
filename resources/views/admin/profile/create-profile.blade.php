@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">

        </div>

        <div class="card-body">
            <form action="{{ route("save-profile") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                    <label for="phone">Phone Number*</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', isset($profile) ? $profile->name : '') }}" required>
                    @if($errors->has('phone'))
                        <p class="help-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('gender') ? 'has-error' : '' }}">
                    <label for="gender">Gender*</label>&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="gender" value="Male"> Male &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="gender" value="Female"> Female
                    @if($errors->has('gender'))
                        <p class="help-block">
                            {{ $errors->first('gender') }}
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
                <div class="form-group {{ $errors->has('academic_major') ? 'has-error' : '' }}">
                    <label for="full_description">Academic Major *</label>
                    <input type="text" id="academic_major" name="academic_major" class="form-control" value="{{ old('academic_major', isset($profile) ? $profile->academic_major : '') }}" required>
                    @if($errors->has('full_description'))
                        <p class="help-block">
                            {{ $errors->first('academic_major') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('part_aws_cloud_club') ? 'has-error' : '' }}">
                    <label for="part_aws_cloud_club">Are you part of an AWS Cloud Club? *</label><br/>
                    <input type="radio" name="part_aws_cloud_club" value="AWS Cloud Club at BRAC University"> AWS Cloud Club at BRAC University <br/>
                    <input type="radio" name="part_aws_cloud_club" value="AWS Cloud Club at KUET"> AWS Cloud Club at KUET <br/>
                    <input type="radio" name="part_aws_cloud_club" value="Other"> Other
                    @if($errors->has('part_aws_cloud_club'))
                        <p class="help-block">
                            {{ $errors->first('part_aws_cloud_club') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('tracks_like') ? 'has-error' : '' }}">
                    <label for="tracks_like">Are you part of an AWS Cloud Club? *</label><br/>
                    <input type="radio" name="tracks_like" value="Technical Track"><a href="#" data-toggle="tooltip" data-placement="top" title="Hands on workshop on building cloud native applications and monetizing using data driven machine learning."> Technical Track</a> <br/>
                    <input type="radio" name="tracks_like" value="Business Track"> <a href="#" data-toggle="tooltip" data-placement="top" title="Hands on workshop on building the next generation billion dollar unicorn and AWS partner program to extend your customer reach.">Business Track</a> <br/>
                    <input type="radio" name="tracks_like" value="Career Track"> <a href="#" data-toggle="tooltip" data-placement="top" title="Educators will learn about AWS student programs, and specific incentives for institutions.">Career Track</a>
                    @if($errors->has('tracks_like'))
                        <p class="help-block">
                            {{ $errors->first('tracks_like') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('tracks_like') ? 'has-error' : '' }}">
                    <label for="tracks_like">How familiar are you with AWS products and services? *</label><br/>
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
                <div class="form-group {{ $errors->has('comments') ? 'has-error' : '' }}">
                    <label for="description">Comments</label>
                    <textarea id="comments" name="comments" class="form-control ">{{ old('comments', isset($profile) ? $profile->comments : '') }}</textarea>
                    @if($errors->has('comments'))
                        <p class="help-block">
                            {{ $errors->first('comments') }}
                        </p>
                    @endif
                </div>
{{--                <div class="form-group {{ $errors->has('photo') ? 'has-error' : '' }}">--}}
{{--                    <label for="photo">{{ trans('cruds.speaker.fields.photo') }}</label>--}}
{{--                    <div class="needsclick dropzone" id="photo-dropzone">--}}

{{--                    </div>--}}
{{--                    @if($errors->has('photo'))--}}
{{--                        <p class="help-block">--}}
{{--                            {{ $errors->first('photo') }}--}}
{{--                        </p>--}}
{{--                    @endif--}}
{{--                    <p class="helper-block">--}}
{{--                        {{ trans('cruds.speaker.fields.photo_helper') }}--}}
{{--                    </p>--}}
{{--                </div>--}}

                <div>
                    <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>
@endsection
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
