@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.events.title_singular') }}
    </div>

    <div class="card-body">
{{--        {!! Form::open(['route' => 'admin.events.store','files' => true] ) !!}--}}
        <form action="{{ route("admin.events.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
        @include ('admin.events.form')
        </form>
{{--        {!! Form::close() !!}--}}

{{--        <form action="{{ route("admin.events.store") }}" method="POST" enctype="multipart/form-data">--}}
{{--            @csrf--}}
{{--            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">--}}
{{--                <label for="name">{{ trans('cruds.sponsor.fields.name') }}*</label>--}}
{{--                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($sponsor) ? $sponsor->name : '') }}" required>--}}
{{--                @if($errors->has('name'))--}}
{{--                    <p class="help-block">--}}
{{--                        {{ $errors->first('name') }}--}}
{{--                    </p>--}}
{{--                @endif--}}
{{--                <p class="helper-block">--}}
{{--                    {{ trans('cruds.sponsor.fields.name_helper') }}--}}
{{--                </p>--}}
{{--            </div>--}}



{{--            <div class="form-group {{ $errors->has('link') ? 'has-error' : '' }}">--}}
{{--                <label for="link">{{ trans('cruds.sponsor.fields.link') }}</label>--}}
{{--                <input type="text" id="link" name="link" class="form-control" value="{{ old('link', isset($sponsor) ? $sponsor->link : '') }}">--}}
{{--                @if($errors->has('link'))--}}
{{--                    <p class="help-block">--}}
{{--                        {{ $errors->first('link') }}--}}
{{--                    </p>--}}
{{--                @endif--}}
{{--                <p class="helper-block">--}}
{{--                    {{ trans('cruds.sponsor.fields.link_helper') }}--}}
{{--                </p>--}}
{{--            </div>--}}
{{--            <div>--}}
{{--                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">--}}
{{--            </div>--}}
{{--        </form>--}}
    </div>
</div>
@endsection

