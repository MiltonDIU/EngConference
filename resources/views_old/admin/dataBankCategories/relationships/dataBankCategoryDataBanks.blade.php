<div class="m-3">
    @can('data_bank_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.data-banks.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.dataBank.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.dataBank.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-dataBankCategoryDataBanks">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.dataBank.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.dataBank.fields.email') }}
                        </th>
                        <th>
                            {{ trans('cruds.dataBank.fields.is_subscribe') }}
                        </th>
                        <th>
                            {{ trans('cruds.dataBank.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.dataBank.fields.unsubscribe_link') }}
                        </th>
                        <th>
                            {{ trans('cruds.dataBank.fields.data_bank_category') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($dataBanks as $key => $dataBank)
                        <tr data-entry-id="{{ $dataBank->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $dataBank->id ?? '' }}
                            </td>
                            <td>
                                {{ $dataBank->email ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\DataBank::IS_SUBSCRIBE_SELECT[$dataBank->is_subscribe] ?? '' }}
                            </td>
                            <td>
                                {{ $dataBank->name ?? '' }}
                            </td>
                            <td>
                                {{ $dataBank->unsubscribe_link ?? '' }}
                            </td>
                            <td>
                                @foreach($dataBank->data_bank_categories as $key => $item)
                                    <span class="badge badge-info">{{ $item->title_of_data_bank }}</span>
                                @endforeach
                            </td>
                            <td>
                                @can('data_bank_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.data-banks.show', $dataBank->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('data_bank_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.data-banks.edit', $dataBank->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('data_bank_delete')
                                    <form action="{{ route('admin.data-banks.destroy', $dataBank->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
</div>
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('data_bank_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.data-banks.massDestroy') }}",
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
            let table = $('.datatable-dataBankCategoryDataBanks:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
