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
                    {{--                    <tr>--}}
                    {{--                        <th>--}}
                    {{--                            {{ trans('cruds.schedule.fields.id') }}--}}
                    {{--                        </th>--}}
                    {{--                        <td>--}}
                    {{--                            {{ $schedule->id }}--}}
                    {{--                        </td>--}}
                    {{--                    </tr>--}}
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
                    {{--                    <tr>--}}
                    {{--                        <th>--}}
                    {{--                            {{ trans('cruds.schedule.fields.total_seat') }}--}}
                    {{--                        </th>--}}
                    {{--                        <td>--}}
                    {{--                            {{ $schedule->total_seat }}--}}
                    {{--                        </td>--}}
                    {{--                    </tr>--}}
                    {{--                    <tr>--}}
                    {{--                        <th>--}}
                    {{--                            Number of Registration--}}
                    {{--                        </th>--}}
                    {{--                        <td>--}}
                    {{--                            {{ $schedule->users->count() }}--}}
                    {{--                        </td>--}}
                    {{--                    </tr>--}}
                    {{--                    <tr>--}}
                    {{--                        <th>--}}
                    {{--                            {{ trans('cruds.schedule.fields.subtitle') }}--}}
                    {{--                        </th>--}}
                    {{--                        <td>--}}
                    {{--                            {{ $schedule->subtitle }}--}}
                    {{--                        </td>--}}
                    {{--                    </tr>--}}
                    {{--                    <tr>--}}
                    {{--                        <th>--}}
                    {{--                            {{ trans('cruds.schedule.fields.speaker') }}--}}
                    {{--                        </th>--}}
                    {{--                        <td>--}}
                    {{--                            {{ $schedule->speaker->name ?? '' }}--}}
                    {{--                        </td>--}}
                    {{--                    </tr>--}}
                    </tbody>
                </table>
                <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>


        </div>
    </div>
    <div class="m-3">
        <div class="card">
            <div class="card-header">
              Attendance {{ trans('global.list') }}
            </div>

            <div class="card-body">
                <div class="table-responsive">







                    <table class=" table table-bordered table-striped table-hover datatable datatable-programStudents">
                        <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.user.fields.name') }}
                            </th>
                            <th>
                                Reg. ID
                            </th>
                            <th>
                                Attendance
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($schedule->users as $key => $user)
                            @if($user->profile->payment_status=='1')
                                <tr data-entry-id="{{ $user->id }}">
                                    <td>
                                    </td>
                                    <td>
                                        {{ $user->name ?? '' }}
                                    </td>
                                    <td>
                                        {{ $user->profile->identity_no ?? ''   }}
                                    </td>
                                    <td>

                                        <input
                                            type="checkbox"
                                            class="attendance-checkbox"
                                            data-user-id="{{ $user->id }}"
                                            data-schedule-id="{{ $schedule->id }}"
                                            {{ $user->attendance->where('schedule_id', $schedule->id)->where('attendance_status',1)->first() ? 'checked' : '' }}
                                        />

                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
<style>
    /* Increase the size of the checkboxes */
    .attendance-checkbox {
        width: 20px; /* Adjust the width as needed */
        height: 20px; /* Adjust the height as needed */
    }
</style>

@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('student_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.students.massDestroy') }}",
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
                orderCellsTop: true,
                order: [[ 1, 'desc' ]],
                pageLength: 100,
            });
            let table = $('.datatable-programStudents:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.attendance-checkbox');

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const userId = checkbox.getAttribute('data-user-id');
                    const scheduleId = checkbox.getAttribute('data-schedule-id');
                    const attendanceStatus = checkbox.checked ? 1 : 0;

                    // Send an AJAX request to update attendance status
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: 'post',
                        url: '/admin/attendance-present',
                        data: {
                            'userId': userId,
                            'scheduleId': scheduleId,
                            'attendanceStatus': attendanceStatus,
                        },
                        dataType: 'text',
                        success: function (data) {
                            console.log(data);
                            // Update the status in your HTML as needed
                            $('#status' + userId).html(data);
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            console.error('Error:', errorThrown);
                        }
                    });
                });
            });
        });
    </script>

@endsection

