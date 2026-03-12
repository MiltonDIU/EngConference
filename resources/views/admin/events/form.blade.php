<div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.fields.title') }}*</label>
        <div class="col-md-9">
            {!! Form::text('title', null, ['class' => 'form-control', 'id'=>'title', 'required' => true]) !!}
            {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('slug') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.fields.slug') }}*</label>
        <div class="col-md-9">
            {!! Form::text('slug', null, ['class' => 'form-control', 'id'=>'slug', 'required' => 'required']) !!}
            {!! $errors->first('slug', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('sub_title') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.fields.sub_title') }}*</label>
        <div class="col-md-9">
            {!! Form::text('sub_title', null, ['class' => 'form-control', 'required' => 'required']) !!}
            {!! $errors->first('sub_title', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>


<div class="form-group {{ $errors->has('bg') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.fields.bg') }}*</label>
        <div class="col-md-9">
            <div class="needsclick dropzone" id="bg-dropzone">
            </div>
            {!! $errors->first('bg', '<p class="help-block">:message</p>') !!}
            <p class="helper-block">
                {{ trans('cruds.sponsor.fields.logo_helper') }}
            </p>
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('youtube_link') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">Youtube Link</label>
        <div class="col-md-9">
            {!! Form::text('youtube_link', null, ['class' => 'form-control', 'required' => false]) !!}
            {!! $errors->first('youtube_link', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('contact_phone') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">contact_phone *</label>
        <div class="col-md-9">
            {!! Form::text('contact_phone', null, ['class' => 'form-control', 'required' => true]) !!}
            {!! $errors->first('contact_phone', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('contact_email') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">contact_email *</label>
        <div class="col-md-9">
            {!! Form::text('contact_email', null, ['class' => 'form-control', 'required' => true]) !!}
            {!! $errors->first('contact_email', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('contact_address') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">contact_address *</label>
        <div class="col-md-9">
            {!! Form::textarea('contact_address', null, ['class' => 'form-control', 'required' => true,'rows'=>2]) !!}
            {!! $errors->first('contact_address', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('about_where') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">about_where *</label>
        <div class="col-md-9">
            {!! Form::text('about_where', null, ['class' => 'form-control', 'required' => true]) !!}
            {!! $errors->first('about_where', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('about_when') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">about_when *</label>
        <div class="col-md-9">
            {!! Form::text('about_when', null, ['class' => 'form-control', 'required' => true]) !!}
            {!! $errors->first('about_when', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('about_summary') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">about_summary</label>
        <div class="col-md-9">
            {!! Form::textarea('about_summary', null, ['class' => 'form-control', 'required' => true, 'rows'=>3]) !!}
            {!! $errors->first('about_summary', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">description</label>
        <div class="col-md-9">
            {!! Form::textarea('description', null, ['class' => 'form-control', 'required' => false,'rows'=>3]) !!}
            {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('date_time') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">date_time</label>
        <div class="col-md-9">
            {!! Form::datetimeLocal('date_time', (isset($event)) ? $event->date_time : null, ['class' => 'form-control']) !!}
{{--            {!! Form::datetimeLocal('date_time', null, ['class' => 'form-control', 'required' => false]) !!}--}}
            {!! $errors->first('date_time', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('reg_close_time') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">reg_close_time</label>
        <div class="col-md-9">
            {!! Form::datetimeLocal('reg_close_time', null, ['class' => 'form-control', 'required' => false]) !!}
            {!! $errors->first('reg_close_time', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>


<div class="form-group {{ $errors->has('is_active') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">Is Active?</label>
        <div class="col-md-9">
            {!! Form::radio('is_active', 1, ['class' => 'custom-control-input']) !!} Yes
            {!! Form::radio('is_active', 0, ['class' => 'custom-control-input']) !!} No
            {!! $errors->first('is_active', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('is_free') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">Is Free?</label>
        <div class="col-md-9">
            {!! Form::radio('is_free', 1, ['class' => 'custom-control-input']) !!} Yes
            {!! Form::radio('is_free', 0, ['class' => 'custom-control-input']) !!} No
            {!! $errors->first('is_free', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('is_events') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">Is Events?</label>
        <div class="col-md-9">
            {!! Form::radio('is_events', 1, ['class' => 'custom-control-input']) !!} Yes
            {!! Form::radio('is_events', 0, ['class' => 'custom-control-input']) !!} No
            {!! $errors->first('is_events', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('speaker_title') ? 'has-error' : ''}}">
            <div class="row">
                <label for="name" class="col-md-6">{{ trans('cruds.events.fields.speaker_title') }}</label>
                <div class="col-md-6">
                    {!! Form::text('speaker_title', 'Event Speakers', ['class' => 'form-control', 'required' => false]) !!}
                    {!! $errors->first('speaker_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('speaker_sub_title') ? 'has-error' : ''}}">
            <div class="row">
                <label class="col-md-1"></label>
                <label for="name" class="col-md-4">{{ trans('cruds.events.fields.speaker_sub_title') }}</label>
                <div class="col-md-7">
                    {!! Form::text('speaker_sub_title', null, ['class' => 'form-control', 'placeholder'=>'Sub Title', 'required' => false]) !!}
                    {!! $errors->first('speaker_sub_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('speaker_ids') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.speaker_setting') }}</label>
        <div class="col-md-9">
            {!! Form::select('speaker_ids[]', $speakers, $event->speakers??null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
            {!! $errors->first('speaker_ids', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<hr>


<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('hotel_title') ? 'has-error' : ''}}">
            <div class="row">
                <label for="name" class="col-md-6">{{ trans('cruds.events.fields.hotel_title') }}</label>
                <div class="col-md-6">
                    {!! Form::text('hotel_title', 'Hotels', ['class' => 'form-control', 'required' => false]) !!}
                    {!! $errors->first('hotel_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('hotel_sub_title') ? 'has-error' : ''}}">
            <div class="row">
                <label class="col-md-1"></label>
                <label for="name" class="col-md-4">{{ trans('cruds.events.fields.hotel_sub_title') }}</label>
                <div class="col-md-7">
                    {!! Form::text('hotel_sub_title', null, ['class' => 'form-control', 'placeholder'=>'Sub Title', 'required' => false]) !!}
                    {!! $errors->first('hotel_sub_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('hotel_ids') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.hotel_setting') }}</label>
        <div class="col-md-9">
            {!! Form::select('hotel_ids[]', $hotels, $event->hotels??null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
            {!! $errors->first('hotel_ids', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('venue_title') ? 'has-error' : ''}}">
            <div class="row">
                <label for="name" class="col-md-6">{{ trans('cruds.events.fields.venue_title') }}</label>
                <div class="col-md-6">
                    {!! Form::text('venue_title', 'Event Venue', ['class' => 'form-control', 'required' => false]) !!}
                    {!! $errors->first('venue_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('venue_sub_title') ? 'has-error' : ''}}">
            <div class="row">
                <label class="col-md-1"></label>
                <label for="name" class="col-md-4">{{ trans('cruds.events.fields.venue_sub_title') }}</label>
                <div class="col-md-7">
                    {!! Form::text('venue_sub_title', null, ['class' => 'form-control', 'placeholder'=>'Sub Title', 'required' => false]) !!}
                    {!! $errors->first('venue_sub_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('venue_ids') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.venue_setting') }}</label>
        <div class="col-md-9">
            {!! Form::select('venue_ids[]', $venues, $event->venues??null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
            {!! $errors->first('venue_ids', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<hr>



<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('sponsor_title') ? 'has-error' : ''}}">
            <div class="row">
                <label for="name" class="col-md-6">{{ trans('cruds.events.fields.sponsor_title') }}</label>
                <div class="col-md-6">
                    {!! Form::text('sponsor_title', 'Event sponsor', ['class' => 'form-control', 'required' => false]) !!}
                    {!! $errors->first('sponsor_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('sponsor_sub_title') ? 'has-error' : ''}}">
            <div class="row">
                <label class="col-md-1"></label>
                <label for="name" class="col-md-4">{{ trans('cruds.events.fields.sponsor_sub_title') }}</label>
                <div class="col-md-7">
                    {!! Form::text('sponsor_sub_title', null, ['class' => 'form-control', 'placeholder'=>'Sub Title', 'required' => false]) !!}
                    {!! $errors->first('sponsor_sub_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('sponsor_ids') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.sponsors_setting') }}</label>
        <div class="col-md-9">
            {!! Form::select('sponsor_ids[]', $sponsors, $event->sponsors??null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
            {!! $errors->first('sponsor_ids', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<hr>


<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('faq_title') ? 'has-error' : ''}}">
            <div class="row">
                <label for="name" class="col-md-6">{{ trans('cruds.events.fields.faq_title') }}</label>
                <div class="col-md-6">
                    {!! Form::text('faq_title', 'Event faq', ['class' => 'form-control', 'required' => false]) !!}
                    {!! $errors->first('faq_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('faq_sub_title') ? 'has-error' : ''}}">
            <div class="row">
                <label class="col-md-1"></label>
                <label for="name" class="col-md-4">{{ trans('cruds.events.fields.faq_sub_title') }}</label>
                <div class="col-md-7">
                    {!! Form::text('faq_sub_title', null, ['class' => 'form-control', 'placeholder'=>'Sub Title', 'required' => false]) !!}
                    {!! $errors->first('faq_sub_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>


<div class="form-group {{ $errors->has('faq_ids') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.faq_setting') }}</label>
        <div class="col-md-9">
            {!! Form::select('faq_ids[]', $faqs, $event->faqs??null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
            {!! $errors->first('faq_ids', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<hr>




<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('price_title') ? 'has-error' : ''}}">
            <div class="row">
                <label for="name" class="col-md-6">{{ trans('cruds.events.fields.price_title') }}</label>
                <div class="col-md-6">
                    {!! Form::text('price_title', 'Event price', ['class' => 'form-control', 'required' => false]) !!}
                    {!! $errors->first('price_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('price_sub_title') ? 'has-error' : ''}}">
            <div class="row">
                <label class="col-md-1"></label>
                <label for="name" class="col-md-4">{{ trans('cruds.events.fields.price_sub_title') }}</label>
                <div class="col-md-7">
                    {!! Form::text('price_sub_title', null, ['class' => 'form-control', 'placeholder'=>'Sub Title', 'required' => false]) !!}
                    {!! $errors->first('price_sub_title', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>



<div class="form-group {{ $errors->has('price_ids') ? 'has-error' : ''}}">
    <div class="row">
        <label for="name" class="col-md-3">{{ trans('cruds.events.price_setting') }}</label>
        <div class="col-md-9">
            {!! Form::select('price_ids[]', $prices, $event->prices??null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
            {!! $errors->first('price_ids', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<hr>



<div class="form-group">
    <div class="row">
        <label for="name" class="col-md-3"></label>
        <div class="col-md-9">
            <input class="btn btn-primary save-author" type="submit" value="{{ $update?? 'Create' }}">
        </div>
    </div>

</div>


@section('scripts')

    <script>
        Dropzone.options.bgDropzone = {
            url: '{{ route('admin.events.storeMedia') }}',
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
                $('form').find('input[name="bg"]').remove()
                $('form').append('<input type="hidden" name="bg" value="' + response.name + '">')
            },
            removedfile: function (file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="bg"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function () {
                @if(isset($event) && $event->photo)
                var file = {!! json_encode($event->photo) !!}
                this.options.addedfile.call(this, file)
                this.options.thumbnail.call(this, file, file.url)
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="bg" value="' + file.file_name + '">')
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

        var convertName2Alias = function () {
            var title = $(this).val().trim().toLowerCase().replace(/\s+/g, '-');
            var slug = $('#slug').val();
            if (slug == '') {
                $('#slug').val(title);
            }
        };
        $(function () {
            $('#title').on('change', convertName2Alias);
        });

    </script>
@stop
