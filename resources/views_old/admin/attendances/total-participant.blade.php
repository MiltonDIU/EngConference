@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.schedule.title') }}
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
                                Reg. ID
                            </th>
                            <th>
                                Attendance
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.name') }}
                            </th>

                            <th>
                                Email
                            </th>
                            <th>
                                Mobile
                            </th>
<th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($profiles as $profile)
                            @if($profile->payment_status=='1')
                                <tr data-entry-id="{{ $profile->id }}">
                                    <td>
                                    </td>
                                    <td>
                                        {{ $profile->identity_no ?? ''   }}
                                    </td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            class="attendance-checkbox"
                                            data-user-id="{{ $profile->id }}"
                                            {{ $profile->event_attendance =='1' ? 'checked' : '' }}
                                        />

                                    </td>
                                    <td>
                                        {{ $profile->user->name ?? '' }}
                                    </td>
                                    <td>
                                        {{ $profile->user->email ?? '' }}
                                    </td>
                                    <td>
                                        {{ $profile->phone ?? '' }}
                                    </td>
<td></td>
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
                    const attendanceStatus = checkbox.checked ? 1 : 0;

                    if (!checkbox.checked) {
                        // Confirmation dialog for deselection
                        const confirmation = confirm('Are you sure you want to deselect this attendance?');
                        if (!confirmation) {
                            checkbox.checked = true; // Keep the checkbox checked if Cancel is clicked
                            return; // Stop further execution of the script
                        }
                    }

                    // If OK or checkbox is already checked, proceed with AJAX request
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: 'post',
                        url: '/admin/eventAttendance',
                        data: {
                            'userId': userId,
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

