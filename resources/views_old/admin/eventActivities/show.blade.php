@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.eventActivity.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.event-activities.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.eventActivity.fields.id') }}
                        </th>
                        <td>
                            {{ $eventActivity->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.eventActivity.fields.title') }}
                        </th>
                        <td>
                            {{ $eventActivity->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.eventActivity.fields.link') }}
                        </th>
                        <td>
                            {{ $eventActivity->link }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.eventActivity.fields.summary') }}
                        </th>
                        <td>
                            {{ $eventActivity->summary }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.eventActivity.fields.is_active') }}
                        </th>
                        <td>
                            {{ App\Models\EventActivity::IS_ACTIVE_SELECT[$eventActivity->is_active] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.eventActivity.fields.feature_image') }}
                        </th>
                        <td>
                            @if($eventActivity->feature_image)
                                <a href="{{ $eventActivity->feature_image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $eventActivity->feature_image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.event-activities.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection
