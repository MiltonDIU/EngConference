@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Custom Mail
        </div>
        <div class="card-body">
            <form action="@if(isset($customMail)){{ route('admin.custom-mail.update',$customMail->id) }}@else{{ route("admin.custom-mail.store") }}@endif" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($customMail))
                    @method('PUT')
                @endif

                <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
                    <label for="name">Subject*</label>
                    <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject', isset($customMail) ? $customMail->subject : '') }}" required>
                    @if($errors->has('subject'))
                        <p class="help-block">
                            {{ $errors->first('subject') }}
                        </p>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('mail_body') ? 'has-error' : '' }}">
                    <label for="'mail|_body">Value</label>
                    <textarea  name="mail_body" class="form-control ckeditor2"> {{ old('mail_body', isset($customMail) ? $customMail->mail_body : '') }}</textarea>
                    @if($errors->has('mail_body'))
                        <p class="help-block">
                            {{ $errors->first('mail_body') }}
                        </p>
                    @endif
                </div>


                <div class="form-group {{ $errors->has('publication_status') ? 'has-error' : '' }}">
                    <label for="part_aws_cloud_club">Publication Status *</label><br/>
                    <input type="radio" name="publication_status" value="1" @if(isset($customMail->publication_status)){{ $customMail->publication_status == 1 ? 'checked':'' }}@else {{ 'checked' }}@endif> Publish <br/>
                    <input type="radio" name="publication_status" value="0" @if(isset($customMail->publication_status)){{ $customMail->publication_status == 0 ? 'checked':'' }}@endif> UnPublish <br/>
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
@section('scripts')
{{--    @parent--}}
{{--    <script src="https://cdn.tiny.cloud/1/tyqw9kvwcj1kjrf4fiymld03mwtrw3dl9ww9mtg6c92tnzcr/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>--}}
{{--    <script>--}}
{{--        tinymce.init({--}}
{{--            selector: 'textarea',--}}
{{--            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',--}}
{{--            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',--}}
{{--        });--}}
{{--    </script>--}}

<script>
    Dropzone.options.featureImageDropzone = {
        url: '{{ route('admin.posts.storeMedia') }}',
        maxFilesize: 3, // MB
        acceptedFiles: '.jpeg,.jpg,.png,.gif,.webp',
        maxFiles: 1,
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        params: {
            size: 3,
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
            @if(isset($post) && $post->feature_image)
            var file = {!! json_encode($post->feature_image) !!}
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
<script>
    $(document).ready(function () {
        function SimpleUploadAdapter(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
                return {
                    upload: function() {
                        return loader.file
                            .then(function (file) {
                                return new Promise(function(resolve, reject) {
                                    // Init request
                                    var xhr = new XMLHttpRequest();
                                    xhr.open('POST', '{{ route('admin.posts.storeCKEditorImages') }}', true);
                                    xhr.setRequestHeader('x-csrf-token', window._token);
                                    xhr.setRequestHeader('Accept', 'application/json');
                                    xhr.responseType = 'json';

                                    // Init listeners
                                    var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                                    xhr.addEventListener('error', function() { reject(genericErrorText) });
                                    xhr.addEventListener('abort', function() { reject() });
                                    xhr.addEventListener('load', function() {
                                        var response = xhr.response;

                                        if (!response || xhr.status !== 201) {
                                            return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                                        }

                                        $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                                        resolve({ default: response.url });
                                    });

                                    if (xhr.upload) {
                                        xhr.upload.addEventListener('progress', function(e) {
                                            if (e.lengthComputable) {
                                                loader.uploadTotal = e.total;
                                                loader.uploaded = e.loaded;
                                            }
                                        });
                                    }

                                    // Send request
                                    var data = new FormData();
                                    data.append('upload', file);
                                    data.append('crud_id', '{{ $post->id ?? 0 }}');
                                    xhr.send(data);
                                });
                            })
                    }
                };
            }
        }

        var allEditors = document.querySelectorAll('.ckeditor2');
        for (var i = 0; i < allEditors.length; ++i) {
            ClassicEditor.create(
                allEditors[i], {
                    extraPlugins: [SimpleUploadAdapter]
                }
            );
        }
    });
</script>


@endsection


