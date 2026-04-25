@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Coupon
        </div>

        <div class="card-body">
            <form action="{{ route("admin.domain.update",$domain->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group {{ $errors->has('concern_name') ? 'has-error' : '' }}">
                    <label for="name">Concern name*</label>
                    <input type="text" id="concern_name" name="concern_name" class="form-control" placeholder="Your name in short" value="{{ $domain->concern_name }}" required>
                    @if($errors->has('concern_name'))
                        <p class="help-block">
                            {{ $errors->first('concern_name') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('domain_name') ? 'has-error' : '' }}">
                    <label for="domain_name">Value</label>
                    <input type="text" id="domain_name" name="domain_name" class="form-control" value="{{ old('domain_name', isset($domain) ? $domain->domain_name : '') }}" required>
                    @if($errors->has('domain_name'))
                        <p class="help-block">
                            {{ $errors->first('domain_name') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('part_aws_cloud_club') ? 'has-error' : '' }}">
                    <label for="part_aws_cloud_club">Status*</label><br/>
                    <input type="radio" name="status" value="1" {{ $domain->status == 1?'checked':'' }}> Publish <br/>
                    <input type="radio" name="status" value="0" {{ $domain->status == 0?'checked':'' }}> UnPublish <br/>
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

