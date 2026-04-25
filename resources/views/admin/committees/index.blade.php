@extends('layouts.admin')
@section('content')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route('admin.committees.create') }}">
            Add Committee
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Committees List
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable datatable-Committee">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Parent Committee</th>
                        <th>Name</th>
                        <th>Section</th>
                        <th>Sort Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($committees as $key => $committee)
                        <tr data-entry-id="{{ $committee->id }}">
                            <td></td>
                            <td>{{ $committee->id }}</td>
                            <td>{{ $committee->committeeType->name ?? 'N/A' }}</td>
                            <td>{{ $committee->parent->name ?? '-- Root --' }}</td>
                            <td>{{ $committee->name }}</td>
                            <td>{{ $committee->section ?? '' }}</td>
                            <td>{{ $committee->sort_order }}</td>
                            <td>
                                <a class="btn btn-xs btn-info" href="{{ route('admin.committees.edit', $committee->id) }}">
                                    Edit
                                </a>
                                <form action="{{ route('admin.committees.destroy', $committee->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
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
            url: "{{ route('admin.committees.massDestroy') }}",
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
        $('.datatable-Committee:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    })
</script>
@endsection
