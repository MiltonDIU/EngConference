@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Coupon
        </div>

        <div class="card-body">
            <form action="{{ route("admin.domain.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group {{ $errors->has('concern_name') ? 'has-error' : '' }}">
                    <label for="name">Concern Name*</label>
                    <input type="text" id="title" name="concern_name" class="form-control" value="{{ old('concern_name', isset($domain) ? $domain->concern_name : '') }}" required>
                    @if($errors->has('concern_name'))
                        <p class="help-block">
                            {{ $errors->first('concern_name') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('domain_name') ? 'has-error' : '' }}">
                    <label for="domain_name">Doamin Name</label>
                    <input type="text" id="value" name="domain_name" class="form-control" value="{{ old('domain_name', isset($domain) ? $domain->domain_name : '') }}" required>
                    @if($errors->has('domain_name'))
                        <p class="help-block">
                            {{ $errors->first('domain_name') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                    <label for="part_aws_cloud_club">Status *</label><br/>
                    <input type="radio" name="status" value="1"> Publish <br/>
                    <input type="radio" name="status" value="0"> UnPublish <br/>
                    @if($errors->has('status'))
                        <p class="help-block">
                            {{ $errors->first('status') }}
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

