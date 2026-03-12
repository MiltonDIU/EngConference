<div class="m-3">
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.referral.title_singular') }}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-divisionUsers">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.referral.fields.id') }}
                        </th>
                        <th>
                            IP Address
                        </th>
                        <th>
                            Registered ID
                        </th>

                        <th>
                            Payment Status
                        </th>
                        <th>
                            Click Time
                        </th>
                        <th>
                            Registration Time
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
                                {{ $referral->ip_address ?? '' }}
                            </td>
                            <td>
                                @if($referral->user_id!=null)
                                    <a href="{{ route('admin.users.show', $referral->user_id) }}">
                                        {{ $referral->user_id ?? '' }}
                                    </a>
                                @else
                                    <span class="badge badge-danger">Not Registered</span>
                                @endif
                            </td>
                            <td>
                                @if($referral->user_id!=null)

                                    @if($referral->user->payment_status=='1')
                                        <span class="badge badge-success">Paid</span>
                                    @else
                                        <span class="badge badge-danger">Not Paid</span>
                                    @endif
                                @else
                                    <span class="badge badge-danger">Not Registered</span>
                                @endif

                            </td>
                            <td>
                                {{ $referral->created_at ?? '' }}
                            </td>
                            <td>
                                {{ $referral->updated_at ?? '' }}
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
            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [[ 1, 'desc' ]],
                pageLength: 100,
            });
            let table = $('.datatable-divisionUsers:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
