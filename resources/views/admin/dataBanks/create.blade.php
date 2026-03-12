@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.dataBank.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.data-banks.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="email">{{ trans('cruds.dataBank.fields.email') }}</label>
                    <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email') }}" required>
                    @if($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.dataBank.fields.email_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.dataBank.fields.is_subscribe') }}</label>
                    <select class="form-control {{ $errors->has('is_subscribe') ? 'is-invalid' : '' }}" name="is_subscribe" id="is_subscribe" required>
                        <option value disabled {{ old('is_subscribe', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\DataBank::IS_SUBSCRIBE_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('is_subscribe', '1') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('is_subscribe'))
                        <span class="text-danger">{{ $errors->first('is_subscribe') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.dataBank.fields.is_subscribe_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="name">{{ trans('cruds.dataBank.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}">
                    @if($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.dataBank.fields.name_helper') }}</span>
                </div>
{{--                <div class="form-group">--}}
{{--                    <label for="unsubscribe_link">{{ trans('cruds.dataBank.fields.unsubscribe_link') }}</label>--}}
{{--                    <input class="form-control {{ $errors->has('unsubscribe_link') ? 'is-invalid' : '' }}" type="text" name="unsubscribe_link" id="unsubscribe_link" value="{{ old('unsubscribe_link', '') }}">--}}
{{--                    @if($errors->has('unsubscribe_link'))--}}
{{--                        <span class="text-danger">{{ $errors->first('unsubscribe_link') }}</span>--}}
{{--                    @endif--}}
{{--                    <span class="help-block">{{ trans('cruds.dataBank.fields.unsubscribe_link_helper') }}</span>--}}
{{--                </div>--}}
                <div class="form-group">
                    <label class="required" for="data_bank_categories">{{ trans('cruds.dataBank.fields.data_bank_category') }}</label>
                    <div style="padding-bottom: 4px">
                        <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                    </div>
                    <select class="form-control select2 {{ $errors->has('data_bank_categories') ? 'is-invalid' : '' }}" name="data_bank_categories[]" id="data_bank_categories" multiple required>
                        @foreach($data_bank_categories as $id => $data_bank_category)
                            <option value="{{ $id }}" {{ in_array($id, old('data_bank_categories', [])) ? 'selected' : '' }}>{{ $data_bank_category }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('data_bank_categories'))
                        <span class="text-danger">{{ $errors->first('data_bank_categories') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.dataBank.fields.data_bank_category_helper') }}</span>
                </div>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>



@endsection
