@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Coupon
        </div>

        <div class="card-body">
            <form action="{{ route("admin.coupon.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                    <label for="name">Title*</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Your name in short" value="{{ old('title', isset($coupon) ? $coupon->title : '') }}" required>
                    @if($errors->has('title'))
                        <p class="help-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('value') ? 'has-error' : '' }}">
                    <label for="description">Value</label>
                    <input type="number" id="value" name="value" class="form-control" value="{{ old('value', isset($coupon) ? $coupon->value : '') }}" required>
                    @if($errors->has('value'))
                        <p class="help-block">
                            {{ $errors->first('value') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('full_description') ? 'has-error' : '' }}">
                    <label for="full_description"> Expire Date</label>
                    <input type="date" id="value" name="expire_date" class="form-control" value="{{ old('expire_date', isset($coupon) ? $coupon->expire_date : '') }}" required>
                    @if($errors->has('expire_date'))
                        <p class="help-block">
                            {{ $errors->first('expire_date') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email"> Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', isset($coupon) ? $coupon->email : '') }}">
                    @if($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('is_domain') ? 'has-error' : '' }}">
                    <label for="part_aws_cloud_club">This coupon used only DEN users *</label><br/>
                    <input type="radio" name="is_domain" value="1" checked> Yes <br/>
                    <input type="radio" name="is_domain" value="0"> No <br/>
                    @if($errors->has('is_domain'))
                        <p class="help-block">
                            {{ $errors->first('is_domain') }}
                        </p>
                    @endif
                </div>


                <div class="form-group {{ $errors->has('publication_status') ? 'has-error' : '' }}">
                    <label for="part_aws_cloud_club">Are you part of an AWS Cloud Club? *</label><br/>
                    <input type="radio" name="publication_status" value="1" checked> Publish <br/>
                    <input type="radio" name="publication_status" value="0"> UnPublish <br/>
                    @if($errors->has('publication_status'))
                        <p class="help-block">
                            {{ $errors->first('publication_status') }}
                        </p>
                    @endif
                </div>
                <div>
                    <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>
@endsection

