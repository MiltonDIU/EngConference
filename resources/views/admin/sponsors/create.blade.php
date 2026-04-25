@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.sponsor.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.sponsors.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.sponsor.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($sponsor) ? $sponsor->name : '') }}" required>
                @if($errors->has('name'))
                    <p class="help-block">
                        {{ $errors->first('name') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.sponsor.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('logo') ? 'has-error' : '' }}">
                <label for="logo">{{ trans('cruds.sponsor.fields.logo') }}</label>
                <div class="needsclick dropzone" id="logo-dropzone">

                </div>
                @if($errors->has('logo'))
                    <p class="help-block">
                        {{ $errors->first('logo') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.sponsor.fields.logo_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('link') ? 'has-error' : '' }}">
                <label for="link">{{ trans('cruds.sponsor.fields.link') }}</label>
                <input type="text" id="link" name="link" class="form-control" value="{{ old('link', isset($sponsor) ? $sponsor->link : '') }}">
                @if($errors->has('link'))
                    <p class="help-block">
                        {{ $errors->first('link') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.sponsor.fields.link_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('serial') ? 'has-error' : '' }}">
                <label for="serial">{{ trans('cruds.sponsor.fields.serial') }}</label>
                <input type="number" id="serial" name="serial" class="form-control" value="{{ old('serial', isset($sponsor) ? $sponsor->serial : '0') }}">
                @if($errors->has('serial'))
                    <p class="help-block">
                        {{ $errors->first('serial') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.sponsor.fields.serial_helper') }}
                </p>
            </div>

            <div class="form-group">
                <label class="required" for="sponsor_type_id">Sponsor Type</label>
                <select class="form-control select2 {{ $errors->has('sponsor_type_id') ? 'is-invalid' : '' }}" name="sponsor_type_id" id="sponsor_type_id" required>
                    @foreach($sponsorTypes as $id => $entry)
                        <option value="{{ $id }}" {{ old('sponsor_type_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('sponsor_type_id'))
                    <span class="text-danger">{{ $errors->first('sponsor_type_id') }}</span>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.sponsor.fields.sponsor_type_id_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                <label for="serial">Amount/Value</label>
                <input type="number" id="amount" name="amount" class="form-control" value="{{ old('amount', isset($sponsor) ? $sponsor->amount : '0') }}">
                @if($errors->has('amount'))
                    <p class="help-block">
                        {{ $errors->first('amount') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.sponsor.fields.amount_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('note') ? 'has-error' : '' }}">
                <label for="note">{{ trans('cruds.sponsor.fields.note') }}</label>
                <input type="text" id="note" name="note" class="form-control" value="{{ old('note', isset($sponsor) ? $sponsor->note : '') }}">
                @if($errors->has('note'))
                    <p class="help-block">
                        {{ $errors->first('note') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.sponsor.fields.note_helper') }}
                </p>
            </div>

            <div>
                <input class="btn btn-primary" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    Dropzone.options.logoDropzone = {
    url: '{{ route('admin.sponsors.storeMedia') }}',
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
      $('form').find('input[name="logo"]').remove()
      $('form').append('<input type="hidden" name="logo" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="logo"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($sponsor) && $sponsor->logo)
      var file = {!! json_encode($sponsor->logo) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="logo" value="' + file.file_name + '">')
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
@stop
