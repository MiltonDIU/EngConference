@extends('layouts.admin')
@section('content')
    <div class="card">
        @can('profile_edit')
            <div class="card-body">
                <form action="{{ route('send-message') }}" method="POST">
                    @csrf
                    <div class="form-group {{ $errors->has('email_body') ? 'has-error' : ''}}">
                        <div class="row">
                            <label for="name" class="col-md-3">Select Message*</label>
                            <div class="col-md-9">
                                <select name="email_id" class="form-control">
                                    @foreach($emails as $email)
                                        <option value="{{ $email->id }}">{{ $email->subject }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('permissions') ? 'has-error' : '' }}">
                        <div class="row">
                            <label for="name" class="col-md-3">Select Group*</label>
                            <div class="col-md-9">
                                <label for="permissions">
                                    <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                                    <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span>
                                </label>
                                <select name="user_groups[]" id="permissions" class="form-control select2" multiple="multiple" required>
                                    <option value="0" >Payment NotComplete</option>
                                    <option value="1" >Payment Complete</option>
                                    <option value="2" >Payment Try</option>
                                    <option value="3" >Payment Cancel</option>
                                </select>
                                @if($errors->has('permissions'))
                                    <p class="help-block">
                                        {{ $errors->first('permissions') }}
                                    </p>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.role.fields.permissions_helper') }}
                                </p>

                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('den_users') ? 'has-error' : ''}}">
                        <div class="row">
                            <label for="name" class="col-md-3">Select User Types*</label>
                            <div class="col-md-9">
                                <select name="den_users" class="form-control">
                                        <option value="1">Send Email With DEN Users</option>
                                        <option value="0">Send Email Without DEN Users</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 offset-3 form-group">
                        <input type="submit" name="submit" value="Send" class="btn btn-success">
                    </div>
                </form>
            </div>
        @endcan
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
                            Registrerd For
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
                        <th>
                            Action
                        </th>

                    </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                    @foreach($profiles as $key => $profile)
                        @if($profile->user)
                        <tr data-entry-id="{{ $profile->id }}">
                            <td>

                            </td>
                            <td>
                                 @php
                                    $number = $profile->identity_no;
                                    $serial = str_pad($number, 4, '0', STR_PAD_LEFT);
                                @endphp
                                @if($profile->payment_status==1 && $profile->identity_no==null)
                                    <a href="{{ route('generateIds',[$profile->id]) }}">
                                        {{ 'Generate Ids' }}
                                    </a>
                                    @else
                                    {{ $serial }}
                                @endif

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


@if($profile->user!=null)
       @foreach($profile->user->schedules as $schedule)

               {{ $schedule->title??'' }}
<br>
        <span class="badge-info"> Day: {{ $schedule->day_number.'--'.$schedule->start_time }} </span>
           <br>
       @endforeach
        @endif
                            </td>
                            <td>
                                {{ date('d/m/Y', strtotime($profile->created_at)) ?? '' }}
                            </td>
                            <td>
{{ $profile->coupon_code ?? '' }}

{{--                                @if($profile->payment_status==1)--}}
{{--                                    @if($profile->coupon_code!=null)--}}
{{--                                        {{ $profile->coupon_code }}--}}
{{--                                    @else--}}
{{--                                        {{ $profile->pay_amount }}--}}
{{--                                    @endif--}}
{{--                                @endif--}}
{{--                                {{ $profile->payment_status==1 ? $profile->coupon_code!=null?$profile->coupon_code:$profile->pay_amount:"" }}--}}
                            </td>

                            <td>
                                {{ $profile->pay_amount ?? '' }}

                            </td>
                            @can('profile_edit')
                            <td>{{ $profile->institute_name??'' }}</td>
                             @endcan
                            <td>
                                @if($profile->payment_status == '1')
                                    <button class="btn btn-success">Payment Complete</button>
                                @else

                                    @if($settings['seat_is_full']=='false')
                                        <button class="btn btn-info" style="float: left;margin-right: 5px">Pending</button>
                                    <br>
                                    <br>
                                        @php
                                            $domain = explode('@', $profile->user->email);
                                        @endphp

                                        @if($settings['special_discount_is_true']=='true' and $profile->coupon_code==null and $profile->special_coupon=='REGSP300' and (in_array($domain[1], $allowedDomain)!=true) )
                                            <form action="{{ route('payNow') }}" method="post" style="width: 50px;float: left;">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $profile->user_id }}">
                                                <input type="hidden" name="special_discount" value="REGSP300">
                                                <input  class="btn btn-danger" type="submit" value="Pay With Coupon extra {{ $settings['special_discount']??"0" }}% (REGSP300)">
                                            </form>
                                        @else
                                            <form action="{{ route('payNow') }}" method="post" style="width: 50px;float: left">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $profile->user_id }}">
                                                <input  class="btn btn-danger" type="submit" value="Pay Now">
                                            </form>

                                        @endif

                                    @else
                                        <button class="btn btn-primary" style="float: left;margin-right: 5px">The Event is Temporarily Postponed</button>
                                    @endcan



                                @endif
{{--                                @if($profile->user_id == 1681)--}}
{{--                                    <form action="{{ route('payNow') }}" method="post" style="width: 50px;float: left">--}}
{{--                                        @csrf--}}
{{--                                        <input type="hidden" name="user_id" value="{{ $profile->user_id }}">--}}
{{--                                        <input  class="btn btn-danger" type="submit" value="Pay Now">--}}
{{--                                    </form>--}}
{{--                                @endif--}}
                            </td>



                            <td>
                                @can('profile_edit')
                                    <a href="{{ route('edit-profile',['id' => $profile->id ]) }}" class="btn btn-primary">Edit</a>
                                @endcan
                            </td>
                        </tr>
                        @endif
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
