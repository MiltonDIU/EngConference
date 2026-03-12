@extends('layouts.admin')
@section('content')
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
                            &nbsp;
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
                            Registrerd For
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            Coupon
                        </th>
                        
                      
                        <th>Institution Name </th>
                          <th>Academic Major</th>
                            <th>Are you part of an AWS Cloud Club?</th>
                             
                                <th>How familiar are you with AWS products and services? </th>
                                <th>Any question, comment, or suggestion?</th>
                                <th>Do you have any PRODUCTION application running in AWS?</th>
                                  <th>
                            Payment
                        </th>
                        

                    </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                    @foreach($profiles as $key => $profile)
                        <tr data-entry-id="{{ $profile->id }}">
                            <td>

                            </td>
                            <td>
                                @php
                                    $number = $profile->user_id;
                                    $serial = str_pad($number, 4, '0', STR_PAD_LEFT);
                                @endphp
                                {{ $serial }}
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
                            <td>
                                {{ $profile->tracks_like ?? '' }}
                            </td>
                            <td>
                                {{ date('d/m/Y', strtotime($profile->created_at)) ?? '' }}
                            </td>
                        
                             
                             
                            <td>
                                @if($profile->payment_status==1)
                                    @if($profile->coupon_code!=null)
                                        {{ $profile->coupon_code }}
                                    @else
                                        {{ $profile->pay_amount }}
                                    @endif
                                @endif
                                <!--{{ $profile->payment_status==1 ? $profile->coupon_code!=null?$profile->coupon_code:$profile->pay_amount:"" }}-->
                            </td>
                                    <td>{{ $profile->institute_name??'' }}</td>
                            <td>{{ $profile->academic_major??'' }}</td>
                            <td>{{ $profile->part_aws_cloud_club??'' }}</td>
                             <td>{{ $profile->aws_familiar??'' }}</td>
                             <td>{{ $profile->comments??'' }}</td>
                             <td>{{ $profile->production_app??'' }} <br>
                             {{ $profile->application_url??'' }}
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