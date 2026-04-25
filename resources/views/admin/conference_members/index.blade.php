@extends('layouts.admin')
@section('content')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route('admin.conference-members.create') }}">
            Add Committee  Member
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Committee  Members List
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable datatable-ConferenceMember">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Institution</th>
                        <th>Email</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $key => $member)
                        <tr data-entry-id="{{ $member->id }}">
                            <td></td>
                            <td>{{ $member->id }}</td>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->designation ?? '' }}</td>
                            <td>{{ $member->institution ?? '' }}</td>
                            <td>{{ $member->email ?? '' }}</td>
                            <td>{{ $member->is_active ? 'Yes' : 'No' }}</td>
                            <td>
                                <a class="btn btn-xs btn-info" href="{{ route('admin.conference-members.edit', $member->id) }}">
                                    Edit
                                </a>
                                <form action="{{ route('admin.conference-members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="submit" class="btn btn-xs btn-danger" value="Delete">
                                </form>
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
        let deleteButton = {
            text: 'Delete Selected',
            url: "{{ route('admin.conference-members.massDestroy') }}",
            className: 'btn-danger',
            action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                    return $(entry).data('entry-id')
                });
                if (ids.length === 0) { alert('No rows selected!'); return }
                if (confirm('Are you sure?')) {
                    $.ajax({
                        headers: {'x-csrf-token': _token},
                        method: 'POST',
                        url: config.url,
                        data: { ids: ids, _method: 'DELETE' }
                    }).done(function () { location.reload() })
                }
            }
        }
        dtButtons.push(deleteButton)
        $.extend(true, $.fn.dataTable.defaults, { order: [[ 1, 'desc' ]], pageLength: 100 });
        $('.datatable-ConferenceMember:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    })
</script>
@endsection
