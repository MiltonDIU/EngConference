@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.schedule.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ route("admin.schedules.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('day_number') ? 'has-error' : '' }}">
                            <label for="day_number">{{ trans('cruds.schedule.fields.day_number') }}*</label>
                            <input type="number" id="day_number" name="day_number" class="form-control" value="{{ old('day_number', isset($schedule) ? $schedule->day_number : '') }}" step="1" required>
                            @if($errors->has('day_number'))
                                <p class="help-block">
                                    {{ $errors->first('day_number') }}
                                </p>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.schedule.fields.day_number_helper') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('start_time') ? 'has-error' : '' }}">
                            <label for="start_time">{{ trans('cruds.schedule.fields.start_time') }}*</label>
                            <input type="text" id="start_time" name="start_time" class="form-control timepicker" value="{{ old('start_time', isset($schedule) ? $schedule->start_time : '') }}" required>
                            @if($errors->has('start_time'))
                                <p class="help-block">
                                    {{ $errors->first('start_time') }}
                                </p>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.schedule.fields.start_time_helper') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            <label for="title">{{ trans('cruds.schedule.fields.title') }}*</label>
                            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($schedule) ? $schedule->title : '') }}" required>
                            @if($errors->has('title'))
                                <p class="help-block">
                                    {{ $errors->first('title') }}
                                </p>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.schedule.fields.title_helper') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('subtitle') ? 'has-error' : '' }}">
                            <label for="subtitle">{{ trans('cruds.schedule.fields.subtitle') }}</label>
                            <input type="text" id="subtitle" name="subtitle" class="form-control" value="{{ old('subtitle', isset($schedule) ? $schedule->subtitle : '') }}">
                            @if($errors->has('subtitle'))
                                <p class="help-block">
                                    {{ $errors->first('subtitle') }}
                                </p>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.schedule.fields.subtitle_helper') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('speaker_id') ? 'has-error' : '' }}">
                            <label for="speaker">{{ trans('cruds.schedule.fields.speaker') }}</label>

                            <select name="speaker_id[]" id="speaker" class="form-control select2" multiple="multiple">
                                @foreach($speakers as $id => $speaker)
                                    <option value="{{ $id }}" {{ (isset($schedule) && $schedule->speaker ? $schedule->speaker->id : old('speaker_id')) == $id ? 'selected' : '' }}>{{ $speaker }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('speaker_id'))
                                <p class="help-block">
                                    {{ $errors->first('speaker_id') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            <label for="title">{{ trans('cruds.schedule.fields.total_seat') }}*</label>
                            <input type="text" id="total_seat" name="total_seat" class="form-control" value="{{ old('total_seat', isset($schedule) ? $schedule->total_seat : '') }}" required>
                            @if($errors->has('total_seat'))
                                <p class="help-block">
                                    {{ $errors->first('total_seat') }}
                                </p>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.schedule.fields.total_seat_helper') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('cruds.schedule.fields.is_workshop') }}</label>
                            <select class="form-control {{ $errors->has('is_workshop') ? 'is-invalid' : '' }}" name="is_workshop" id="workshop" onchange="toggleExtraFields()">
                                <option value disabled {{ old('is_workshop', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Schedule::IS_WORKSHOP_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('is_workshop', '0') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('is_workshop'))
                                <span class="text-danger">{{ $errors->first('is_workshop') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.schedule.fields.is_workshop_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('cruds.schedule.fields.event_session') }}</label>
                            <select class="form-control {{ $errors->has('event_session') ? 'is-invalid' : '' }}" name="event_session" id="event_session" onchange="toggleExtraFields()">
                                <option value disabled {{ old('event_session', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Schedule::IS_event_session as $key => $label)
                                    <option value="{{ $key }}" {{ old('event_session', '0') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('event_session'))
                                <span class="text-danger">{{ $errors->first('event_session') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.schedule.fields.event_session_helper') }}</span>
                        </div>
                    </div>
                </div>

                <div class="extra-fields">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>About:</label>
                            <textarea name="about" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="col-md-5">
                            <label>Benefits:</label>
                            <div id="benefits-container">
                                <div class="benefit">
                                    <input type="text" name="benefit_title[]" placeholder="Title" class="form-control extra-input">
                                    <input type="text" name="benefit_link[]" placeholder="Link" class="form-control extra-input">

                                </div>
                                <button type="button" class="btn btn-success" onclick="addBenefit()">+</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Tools:</label>
                            <div id="tools-container">
                                <div class="tool">
                                    <input type="text" name="tools[]" placeholder="Tool" class="form-control">
                                </div>
                                <button type="button" class="btn btn-success" onclick="addTool()">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')

    <script>
        function toggleExtraFields() {
            const workshopSelect = document.getElementById('workshop');
            const extraFields = document.querySelector('.extra-fields');
            extraFields.style.display = workshopSelect.value === '1' ? 'inherit' : 'none';
        }

        function addTool() {
            const toolsContainer = document.getElementById('tools-container');
            const newTool = document.createElement('div');
            newTool.className = 'tool';
            newTool.innerHTML = `
        <input type="text" name="tools[]" placeholder="Tool" class="form-control extra-input">
        <button type="button" class="btn btn-danger extra-input" onclick="removeTool(this)">-</button>
      `;
            toolsContainer.appendChild(newTool);
        }

        function removeTool(button) {
            const toolContainer = button.parentElement;
            const toolsContainer = document.getElementById('tools-container');
            toolsContainer.removeChild(toolContainer);
        }

        function addBenefit() {
            const benefitsContainer = document.getElementById('benefits-container');
            const newBenefit = document.createElement('div');
            newBenefit.className = 'benefit';
            newBenefit.innerHTML = `
        <input type="text" name="benefit_title[]" placeholder="Title" class="form-control extra-input">
        <input type="text" name="benefit_link[]" placeholder="Link" class="form-control extra-input">
        <button type="button" class="btn btn-danger extra-input" onclick="removeBenefit(this)">-</button>
      `;
            benefitsContainer.appendChild(newBenefit);
        }

        function removeBenefit(button) {
            const benefitContainer = button.parentElement;
            const benefitsContainer = document.getElementById('benefits-container');
            benefitsContainer.removeChild(benefitContainer);
        }
    </script>
@endpush

@push('style')
    <style>
        .extra-fields {
            display: none;
        }

        .tool,
        .benefit {
            margin-bottom: 10px;
        }
        .extra-input{
            margin: 5px 0px;
        }
    </style>
@endpush
