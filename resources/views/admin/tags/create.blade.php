@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.tag.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.tags.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.tag.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.tag.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="slug">{{ trans('cruds.tag.fields.slug') }}</label>
                    <input class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}" type="text" name="slug" id="slug" value="{{ old('slug', '') }}">
                    @if($errors->has('slug'))
                        <span class="text-danger">{{ $errors->first('slug') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.tag.fields.slug_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.tag.fields.is_active') }}</label>
                    <select class="form-control {{ $errors->has('is_active') ? 'is-invalid' : '' }}" name="is_active" id="is_active">
                        <option value disabled {{ old('is_active', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Tag::IS_ACTIVE_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('is_active', '1') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('is_active'))
                        <span class="text-danger">{{ $errors->first('is_active') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.tag.fields.is_active_helper') }}</span>
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

@section('scripts')
    <script>
        var convertName2Alias = function () {
            var name = $(this).val().trim().toLowerCase().replace(/\s+/g, '-');
            var slug = $('#slug').val();
            if (slug == '') {
                $('#slug').val(name);
            }
        };
        $(function () {
            $('#name').on('change', convertName2Alias);
        });
    </script>
    @section('scripts')
