
    <div class="card">
        <div class="card-header">
            Profile
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Speaker">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            &nbsp;Reg. No.
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Email
                        </th>
                        <th>
                            Phone
                        </th>

                        <th>
                            Date
                        </th>
                        <th>
                            Coupon
                        </th>
                        <th>
                            Amount
                        </th>

                        @can('profile_edit')
                            <th>Institution Name </th>

                        @endcan
                        <th>
                            Payment
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $i = 1; @endphp
                    @foreach($users as $key => $user)
                        @if($user->profile->payment_status=='1')
                            <tr data-entry-id="{{ $user->id }}">
                                <td>

                                </td>
                                <td>
                                    @php
                                        $number =$user->profile->identity_no;
                                        $serial = str_pad($number, 4, '0', STR_PAD_LEFT);
                                    @endphp
                                    @if($user->profile->payment_status==1 && $user->profile->identity_no==null)
                                        <a href="{{ route('generateIds',[$profile->id]) }}">
                                            {{ 'Generate Ids' }}
                                        </a>
                                    @else
                                        {{ $serial }}
                                    @endif

                                </td>
                                <td>
                                    {{ $user->name ?? '' }}
                                </td>
                                <td>
                                    {{ $user->email ?? '' }}
                                </td>
                                <td>
                                    {{ $user->profile->phone ?? '' }}
                                </td>
                                <td>
                                    {{ date('d/m/Y', strtotime($user->profile->created_at)) ?? '' }}
                                </td>
                                <td>
                                    {{ $user->profile->coupon_code ?? '' }}
                                </td>

                                <td>
                                    {{ $user->profile->pay_amount ?? '' }}

                                </td>
                                @can('profile_edit')
                                    <td>{{ $user->profile->institute_name??'' }}</td>
                                @endcan
                                <td>
                                    @if($user->profile->payment_status == '1')
                                        <button class="btn btn-success">Payment Complete</button>
                                    @else

                                    dddddddd
                                    @endif
                                </td>

                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            $.extend(true, $.fn.dataTable.defaults, {
                order: [[ 1, 'desc' ]],
                pageLength: 100,
            });
            $('.datatable-Speaker:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })
    </script>
@endsection
