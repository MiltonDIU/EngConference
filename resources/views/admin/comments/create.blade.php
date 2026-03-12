@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.comment.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.comments.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="comment">{{ trans('cruds.comment.fields.comment') }}</label>
                    <textarea class="form-control {{ $errors->has('comment') ? 'is-invalid' : '' }}" name="comment" id="comment">{{ old('comment') }}</textarea>
                    @if($errors->has('comment'))
                        <span class="text-danger">{{ $errors->first('comment') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.comment.fields.comment_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="post_id">{{ trans('cruds.comment.fields.post') }}</label>
                    <select class="form-control select2 {{ $errors->has('post') ? 'is-invalid' : '' }}" name="post_id" id="post_id">
                        @foreach($posts as $id => $entry)
                            <option value="{{ $id }}" {{ old('post_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('post'))
                        <span class="text-danger">{{ $errors->first('post') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.comment.fields.post_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="user_id">{{ trans('cruds.comment.fields.user') }}</label>
                    <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id">
                        @foreach($users as $id => $entry)
                            <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('user'))
                        <span class="text-danger">{{ $errors->first('user') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.comment.fields.user_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>{{ trans('cruds.comment.fields.is_active') }}</label>
                    <select class="form-control {{ $errors->has('is_active') ? 'is-invalid' : '' }}" name="is_active" id="is_active">
                        <option value disabled {{ old('is_active', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Comment::IS_ACTIVE_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('is_active', '1') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('is_active'))
                        <span class="text-danger">{{ $errors->first('is_active') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.comment.fields.is_active_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="parent_id">{{ trans('cruds.comment.fields.parent') }}</label>
                    <select class="form-control select2 {{ $errors->has('parent') ? 'is-invalid' : '' }}" name="parent_id" id="parent_id">
                        @foreach($parents as $id => $entry)
                            <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('parent'))
                        <span class="text-danger">{{ $errors->first('parent') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.comment.fields.parent_helper') }}</span>
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
