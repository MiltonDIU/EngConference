@extends('layouts.admin')
@section('content')
    @can('data_bank_category_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.data-bank-categories.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.dataBankCategory.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.dataBankCategory.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-DataBankCategory">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.dataBankCategory.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.dataBankCategory.fields.title_of_data_bank') }}
                        </th>
                        <th>
                            {{ trans('cruds.dataBankCategory.fields.is_active') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($dataBankCategories as $key => $dataBankCategory)
                        <tr data-entry-id="{{ $dataBankCategory->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $dataBankCategory->id ?? '' }}
                            </td>
                            <td>
                                {{ $dataBankCategory->title_of_data_bank ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\DataBankCategory::IS_ACTIVE_SELECT[$dataBankCategory->is_active] ?? '' }}
                            </td>
                            <td>
                                @can('data_bank_category_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.data-bank-categories.show', $dataBankCategory->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('data_bank_category_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.data-bank-categories.edit', $dataBankCategory->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('data_bank_category_delete')
                                    <form action="{{ route('admin.data-bank-categories.destroy', $dataBankCategory->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
            @can('data_bank_category_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.data-bank-categories.massDestroy') }}",
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
            let table = $('.datatable-DataBankCategory:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
