@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.referral.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.referrals.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.referral.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.referral.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="email">{{ trans('cruds.referral.fields.email') }}</label>
                    <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" id="email" value="{{ old('email', '') }}" required>
                    @if($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.referral.fields.email_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="coupon_id">{{ trans('cruds.referral.fields.coupon_id') }}</label>
                    <select class="form-control select2 {{ $errors->has('blog_category') ? 'is-invalid' : '' }}" name="coupon_id" id="coupon_id">
                        @foreach($coupons as $id => $entry)
                            <option value="{{ $id }}" {{ old('coupon_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('coupon_id'))
                        <span class="text-danger">{{ $errors->first('coupon_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.referral.fields.coupon_id_helper') }}</span>
                </div>


                <div class="form-group">
                    <label class="required">{{ trans('cruds.referral.fields.is_active') }}</label>
                    <select class="form-control {{ $errors->has('is_active') ? 'is-invalid' : '' }}" name="is_active" id="is_active" required>
                        <option value disabled {{ old('is_active', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Referral::IS_ACTIVE_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('is_active', '1') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('is_active'))
                        <span class="text-danger">{{ $errors->first('is_active') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.referral.fields.is_active_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="avatar">{{ trans('cruds.referral.fields.avatar') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('avatar') ? 'is-invalid' : '' }}" id="avatar-dropzone">
                    </div>
                    @if($errors->has('avatar'))
                        <span class="text-danger">{{ $errors->first('avatar') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.referral.fields.avatar_helper') }}</span>
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
        Dropzone.options.avatarDropzone = {
            url: '{{ route('admin.referrals.storeMedia') }}',
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
                $('form').find('input[name="avatar"]').remove()
                $('form').append('<input type="hidden" name="avatar" value="' + response.name + '">')
            },
            removedfile: function (file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="avatar"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function () {
                @if(isset($referral) && $referral->avatar)
                var file = {!! json_encode($referral->avatar) !!}
                this.options.addedfile.call(this, file)
                this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="avatar" value="' + file.file_name + '">')
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
