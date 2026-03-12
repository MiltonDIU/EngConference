@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.speaker.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.speakers.update", [$speaker->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.speaker.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($speaker) ? $speaker->name : '') }}" onload="convertToSlug(this.value)" onkeyup="convertToSlug(this.value)" required>
                @if($errors->has('name'))
                    <p class="help-block">
                        {{ $errors->first('name') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('slug') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.speaker.fields.slug') }}*</label>
                <input type="text" id="slugValue" name="slug" class="form-control" value="{{ old('slug', isset($speaker) ? $speaker->slug : '') }}" required>
                @if($errors->has('slug'))
                    <p class="help-block">
                        {{ $errors->first('slug') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.slug_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">{{ trans('cruds.speaker.fields.description') }}</label>
                <textarea id="description" name="description" class="form-control ">{{ old('description', isset($speaker) ? $speaker->description : '') }}</textarea>
                @if($errors->has('description'))
                    <p class="help-block">
                        {{ $errors->first('description') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.description_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('full_description') ? 'has-error' : '' }}">
                <label for="full_description">{{ trans('cruds.speaker.fields.full_description') }}</label>
                <textarea id="full_description" name="full_description" class="form-control ">{{ old('full_description', isset($speaker) ? $speaker->full_description : '') }}</textarea>
                @if($errors->has('full_description'))
                    <p class="help-block">
                        {{ $errors->first('full_description') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.full_description_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('speaker_type_id') ? 'has-error' : '' }}">
                <label for="speaker_type_id">{{ trans('cruds.speaker.fields.speaker_type_id') }}</label>
                <select name="speaker_type_id" class="form-control">
                    <option value=""> ========= Select One ========== </option>
                    @foreach($speakerTypes as $speakerType)
                        <option value="{{ $speakerType->id }}" {{ $speakerType->id == $speaker->speaker_type_id?'selected':'' }}>{{ $speakerType->title }}</option>
                    @endforeach
                </select>
                @if($errors->has('speaker_type_id'))
                    <p class="help-block">
                        {{ $errors->first('speaker_type_id') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.speaker_type_id_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('guest_category_id') ? 'has-error' : '' }}">
                <label for="guest_category_id">{{ trans('cruds.speaker.fields.guest_category_id') }}</label>
                <select name="guest_category_id[]" class="form-control js-example-basic-multiple" multiple="multiple">
                    <option value=""> ========= Select One ========== </option>
                    @foreach($guestCategories as $guestCategory)
                        <option value="{{ $guestCategory->id }}" {{ in_array($guestCategory->id, $speakerGuestCategories) ? 'selected' : '' }}>{{ $guestCategory->title }}</option>
                    @endforeach
                </select>
                @if($errors->has('guest_category_id'))
                    <p class="help-block">
                        {{ $errors->first('guest_category_id') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.guest_category_id_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('photo') ? 'has-error' : '' }}">
                <label for="photo">{{ trans('cruds.speaker.fields.photo') }}</label>
                <div class="needsclick dropzone" id="photo-dropzone">

                </div>
                @if($errors->has('photo'))
                    <p class="help-block">
                        {{ $errors->first('photo') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.photo_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('twitter') ? 'has-error' : '' }}">
                <label for="twitter">{{ trans('cruds.speaker.fields.twitter') }}</label>
                <input type="text" id="twitter" name="twitter" class="form-control" value="{{ old('twitter', isset($speaker) ? $speaker->twitter : '') }}">
                @if($errors->has('twitter'))
                    <p class="help-block">
                        {{ $errors->first('twitter') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.twitter_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('facebook') ? 'has-error' : '' }}">
                <label for="facebook">{{ trans('cruds.speaker.fields.facebook') }}</label>
                <input type="text" id="facebook" name="facebook" class="form-control" value="{{ old('facebook', isset($speaker) ? $speaker->facebook : '') }}">
                @if($errors->has('facebook'))
                    <p class="help-block">
                        {{ $errors->first('facebook') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.facebook_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('linkedin') ? 'has-error' : '' }}">
                <label for="linkedin">{{ trans('cruds.speaker.fields.linkedin') }}</label>
                <input type="text" id="linkedin" name="linkedin" class="form-control" value="{{ old('linkedin', isset($speaker) ? $speaker->linkedin : '') }}">
                @if($errors->has('linkedin'))
                    <p class="help-block">
                        {{ $errors->first('linkedin') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.linkedin_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('show_home') ? 'has-error' : '' }}">
                <label for="linkedin">{{ trans('cruds.speaker.fields.show_home') }}</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" id="show_home" name="show_home" value="1" {{ $speaker->show_home == 1?'checked':'' }}> Yes &nbsp;&nbsp;
                <input type="radio" id="show_home" name="show_home" value="0" {{ $speaker->show_home == 0?'checked':'' }}> No
                @if($errors->has('show_home'))
                    <p class="help-block">
                        {{ $errors->first('show_home') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.show_home_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('serial') ? 'has-error' : '' }}">
                <label for="serial">{{ trans('cruds.speaker.fields.serial') }}</label>
                <input type="number" id="serial" name="serial" class="form-control" value="{{ old('serial', isset($speaker) ? $speaker->serial : '') }}">
                @if($errors->has('serial'))
                    <p class="help-block">
                        {{ $errors->first('serial') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.speaker.fields.serial_helper') }}
                </p>
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection

@section('scripts')
<script>
    Dropzone.options.photoDropzone = {
    url: '{{ route('admin.speakers.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="photo"]').remove()
      $('form').append('<input type="hidden" name="photo" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="photo"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($speaker) && $speaker->photo)
      var file = {!! json_encode($speaker->photo) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="photo" value="' + file.file_name + '">')
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
    function convertToSlug( str ) {
        //replace all special characters | symbols with a space
        str = str.replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ')
            .toLowerCase();
        // trim spaces at start and end of string
        str = str.replace(/^\s+|\s+$/gm,'');
        // replace space with dash/hyphen
        str = str.replace(/\s+/g, '-');
        document.getElementById("slugValue").value = str;
        //return str;
    }
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });
</script>
@stop