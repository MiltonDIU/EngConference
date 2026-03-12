@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.schedule.title') }}
        </div>

        <div class="card-body">
            <div class="mb-2">
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.schedule.fields.id') }}
                        </th>
                        <td>
                            {{ $schedule->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.schedule.fields.day_number') }}
                        </th>
                        <td>
                            {{ $schedule->day_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.schedule.fields.start_time') }}
                        </th>
                        <td>
                            {{ $schedule->start_time }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.schedule.fields.title') }}
                        </th>
                        <td>
                            {{ $schedule->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.schedule.fields.total_seat') }}
                        </th>
                        <td>
                            {{ $schedule->total_seat }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Number of Registration
                        </th>
                        <td>
                            {{ $schedule->users->count() }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.schedule.fields.subtitle') }}
                        </th>
                        <td>
                            {{ $schedule->subtitle }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.schedule.fields.speaker') }}
                        </th>
                        <td>
                            {{ $schedule->speaker->name ?? '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>

            <div class="card">
                <div class="tab-content active">
                    <div class="tab-pane active" role="tabpanel">
                        @includeIf('admin.schedules.relationships.schedule_registration', ['users' => $schedule->users])
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
