@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.uploadMedium.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.upload-media.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="title">{{ trans('cruds.uploadMedium.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}">
                    @if($errors->has('title'))
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.uploadMedium.fields.title_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="file_name">{{ trans('cruds.uploadMedium.fields.file_name') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('file_name') ? 'is-invalid' : '' }}" id="file_name-dropzone">
                    </div>
                    @if($errors->has('file_name'))
                        <span class="text-danger">{{ $errors->first('file_name') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.uploadMedium.fields.file_name_helper') }}</span>
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
        var uploadedFileNameMap = {}
        Dropzone.options.fileNameDropzone = {
            url: '{{ route('admin.upload-media.storeMedia') }}',
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 10
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="file_name[]" value="' + response.name + '">')
                uploadedFileNameMap[file.name] = response.name
            },
            removedfile: function (file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedFileNameMap[file.name]
                }
                $('form').find('input[name="file_name[]"][value="' + name + '"]').remove()
            },
            init: function () {
                @if(isset($uploadMedium) && $uploadMedium->file_name)
                var files =
                    {!! json_encode($uploadMedium->file_name) !!}
                    for (var i in files) {
                    var file = files[i]
                    this.options.addedfile.call(this, file)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="file_name[]" value="' + file.file_name + '">')
                }
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
