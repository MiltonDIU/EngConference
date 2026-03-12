@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.dataBankCategory.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.data-bank-categories.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="title_of_data_bank">{{ trans('cruds.dataBankCategory.fields.title_of_data_bank') }}</label>
                    <input class="form-control {{ $errors->has('title_of_data_bank') ? 'is-invalid' : '' }}" type="text" name="title_of_data_bank" id="title_of_data_bank" value="{{ old('title_of_data_bank', '') }}" required>
                    @if($errors->has('title_of_data_bank'))
                        <span class="text-danger">{{ $errors->first('title_of_data_bank') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.dataBankCategory.fields.title_of_data_bank_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.dataBankCategory.fields.is_active') }}</label>
                    <select class="form-control {{ $errors->has('is_active') ? 'is-invalid' : '' }}" name="is_active" id="is_active" required>
                        <option value disabled {{ old('is_active', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\DataBankCategory::IS_ACTIVE_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('is_active', '1') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('is_active'))
                        <span class="text-danger">{{ $errors->first('is_active') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.dataBankCategory.fields.is_active_helper') }}</span>
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
