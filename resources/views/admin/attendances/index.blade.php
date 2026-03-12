@extends('layouts.admin')
@section('content')
    @can('attendance_show')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-2">
                <a class="btn btn-info" href="{{ route("admin.eventAttendance") }}">
                  Event Attendance
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.schedule.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Schedule">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.schedule.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.schedule.fields.day_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.schedule.fields.start_time') }}
                        </th>
                        <th>
                          Duration
                        </th>
                        <th>
                            {{ trans('cruds.schedule.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.schedule.fields.is_workshop') }}
                        </th>
                        <th>
                            {{ trans('cruds.schedule.fields.total_seat') }}
                        </th>
                        <th>
                            Number of Registration
                        </th>
                        <th>
                            Number of Paid Users
                        </th>
                        <th>
                            {{ trans('cruds.schedule.fields.subtitle') }}
                        </th>
                        <th>
                            {{ trans('cruds.schedule.fields.speaker') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($schedules as $key => $schedule)
                        <tr data-entry-id="{{ $schedule->id }}">
                            <td>

                            </td>
                            <td>
                                {{ ++$key }}
                            </td>
                            <td>
                                {{ $schedule->day_number ?? '' }}
                            </td>
                            <td>
                                {{ $schedule->start_time ?? '' }}
                            </td>
                            <td>
                                {{ $schedule->subtitle ?? '' }}
                            </td>
                            <td>
                                {{ $schedule->title ?? '' }}
                            </td>

                            <td>
                                {{ $schedule->is_workshop=='1' ? 'Yes':'No' }}
                            </td>
                            <td>
                                {{ $schedule->total_seat ?? '' }}
                            </td>
                            <td>
                                {{ $schedule->users->count() }}


                            </td>
                            <td>
                                @php
                                    $paid = 0;
                                @endphp
                                @foreach($schedule->users as $key => $user)
                                    @if($user->profile->payment_status=='1')
                                        @php
                                            $paid += 1;
                                        @endphp
                                    @endif
                                @endforeach
                                {{ $paid }}
                            </td>
                            <td>
                                {{ $schedule->subtitle ?? '' }}
                            </td>
                            <td>
                                {{ $schedule->speaker->name ?? '' }}
                            </td>
                            <td>
                                @can('attendance_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.attendances.show', $schedule->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('schedule_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.schedules.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                        return $(entry).data('entry-id')
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')

                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: { ids: ids, _method: 'DELETE' }})
                            .done(function () { location.reload() })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                order: [[ 1, 'desc' ]],
                pageLength: 100,
            });
            $('.datatable-Schedule:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })

    </script>
@endsection
