@extends('layouts.admin')
@section('content')
    @can('event_activity_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.event-activities.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.eventActivity.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.eventActivity.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-EventActivity">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.eventActivity.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.eventActivity.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.eventActivity.fields.link') }}
                        </th>
                        <th>
                            {{ trans('cruds.eventActivity.fields.summary') }}
                        </th>
                        <th>
                            {{ trans('cruds.eventActivity.fields.is_active') }}
                        </th>
                        <th>
                            {{ trans('cruds.eventActivity.fields.feature_image') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($eventActivities as $key => $eventActivity)
                        <tr data-entry-id="{{ $eventActivity->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $eventActivity->id ?? '' }}
                            </td>
                            <td>
                                {{ $eventActivity->title ?? '' }}
                            </td>
                            <td>
                                {{ $eventActivity->link ?? '' }}
                            </td>
                            <td>
                                {{ $eventActivity->summary ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\EventActivity::IS_ACTIVE_SELECT[$eventActivity->is_active] ?? '' }}
                            </td>
                            <td>
                                @if($eventActivity->feature_image)
                                    <a href="{{ $eventActivity->feature_image->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $eventActivity->feature_image->getUrl() }}" width="100px">
                                    </a>
                                @endif
                            </td>
                            <td>
                                @can('event_activity_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.event-activities.show', $eventActivity->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('event_activity_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.event-activities.edit', $eventActivity->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('event_activity_delete')
                                    <form action="{{ route('admin.event-activities.destroy', $eventActivity->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
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
            @can('event_activity_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.event-activities.massDestroy') }}",
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
            let table = $('.datatable-EventActivity:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
