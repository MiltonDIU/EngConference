@extends('layouts.admin')
@section('content')
    @can('referral_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.referrals.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.referral.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.referral.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Referral">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.referral.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.referral.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.referral.fields.email') }}
                        </th>
                        <th>
                            {{ trans('cruds.referral.fields.identification') }}
                        </th>
                        <th>
                            {{ 'Impression' }}
                        </th>
                        <th>
                            {{ 'Coupon' }}
                        </th>
                        <th>
                            {{ trans('cruds.referral.fields.is_active') }}
                        </th>
                        <th>
                            {{ trans('cruds.referral.fields.avatar') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($referrals as $key => $referral)
                        <tr data-entry-id="{{ $referral->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $referral->id ?? '' }}
                            </td>
                            <td>
                                {{ $referral->name ?? '' }}
                            </td>
                            <td>
                                {{ $referral->email ?? '' }}
                            </td>
                            <td>
                                {{ $referral->identification ?? '' }}
                            </td>
                            <td>
                                {{ ($referral->referralVisitor!=null)?count($referral->referralVisitor): '0' }}
                            </td>
                            <td>
                                {{ ($referral->coupon!=null)?$referral->coupon->title: '' }}
                            </td>
                            <td>
                                {{ App\Models\Referral::IS_ACTIVE_SELECT[$referral->is_active] ?? '' }}
                            </td>
                            <td>
                                @if($referral->avatar)
                                    <a href="{{ $referral->avatar->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $referral->avatar->getUrl('thumb') }}">
                                    </a>
                                @endif
                            </td>
                            <td>
                                @can('referral_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.referrals.show', $referral->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('referral_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.referrals.edit', $referral->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('referral_delete')
                                    <form action="{{ route('admin.referrals.destroy', $referral->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
            @can('referral_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.referrals.massDestroy') }}",
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
            let table = $('.datatable-Referral:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
