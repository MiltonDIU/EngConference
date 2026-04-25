@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.eventActivity.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.event-activities.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="title">{{ trans('cruds.eventActivity.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}">
                    @if($errors->has('title'))
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.eventActivity.fields.title_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="link">{{ trans('cruds.eventActivity.fields.link') }}</label>
                    <input class="form-control {{ $errors->has('link') ? 'is-invalid' : '' }}" type="text" name="link" id="link" value="{{ old('link', '') }}">
                    @if($errors->has('link'))
                        <span class="text-danger">{{ $errors->first('link') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.eventActivity.fields.link_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="summary">{{ trans('cruds.eventActivity.fields.summary') }}</label>
                    <textarea class="form-control {{ $errors->has('summary') ? 'is-invalid' : '' }}" name="summary" id="summary">{{ old('summary') }}</textarea>
                    @if($errors->has('summary'))
                        <span class="text-danger">{{ $errors->first('summary') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.eventActivity.fields.summary_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.eventActivity.fields.is_active') }}</label>
                    <select class="form-control {{ $errors->has('is_active') ? 'is-invalid' : '' }}" name="is_active" id="is_active" required>
                        <option value disabled {{ old('is_active', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\EventActivity::IS_ACTIVE_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('is_active', '1') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('is_active'))
                        <span class="text-danger">{{ $errors->first('is_active') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.eventActivity.fields.is_active_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="feature_image">{{ trans('cruds.eventActivity.fields.feature_image') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('feature_image') ? 'is-invalid' : '' }}" id="feature_image-dropzone">
                    </div>
                    @if($errors->has('feature_image'))
                        <span class="text-danger">{{ $errors->first('feature_image') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.eventActivity.fields.feature_image_helper') }}</span>
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
        Dropzone.options.featureImageDropzone = {
            url: '{{ route('admin.event-activities.storeMedia') }}',
            maxFilesize: 1, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 1,
                width: 4096,
                height: 4096
            },
            success: function (file, response) {
                $('form').find('input[name="feature_image"]').remove()
                $('form').append('<input type="hidden" name="feature_image" value="' + response.name + '">')
            },
            removedfile: function (file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="feature_image"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function () {
                @if(isset($eventActivity) && $eventActivity->feature_image)
                var file = {!! json_encode($eventActivity->feature_image) !!}
                this.options.addedfile.call(this, file)
                this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="feature_image" value="' + file.file_name + '">')
                this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function (file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }

    </script>
@endsection
