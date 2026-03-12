@extends('layouts.admin')
@section('content')
    <div class="card">
        @can('profile_edit')
            <!--<div class="card-body">-->
            <!--    <form action="{{ route('send-message') }}" method="POST">-->
            <!--        @csrf-->
            <!--        <div class="form-group {{ $errors->has('email_body') ? 'has-error' : ''}}">-->
            <!--            <div class="row">-->
            <!--                <label for="name" class="col-md-3">Select Message*</label>-->
            <!--                <div class="col-md-9">-->
            <!--                    <select name="email_id" class="form-control">-->
            <!--                        @foreach($emails as $email)-->
            <!--                            <option value="{{ $email->id }}">{{ $email->subject }}</option>-->
            <!--                        @endforeach-->
            <!--                    </select>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--        <div class="form-group {{ $errors->has('permissions') ? 'has-error' : '' }}">-->
            <!--            <div class="row">-->
            <!--                <label for="name" class="col-md-3">Select Group*</label>-->
            <!--                <div class="col-md-9">-->
            <!--                    <label for="permissions">-->
            <!--                        <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>-->
            <!--                        <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span>-->
            <!--                    </label>-->
            <!--                    <select name="user_groups[]" id="permissions" class="form-control select2" multiple="multiple" required>-->
            <!--                        <option value="0" >Payment NotComplete</option>-->
            <!--                        <option value="1" >Payment Complete</option>-->
            <!--                        <option value="2" >Payment Try</option>-->
            <!--                    </select>-->
            <!--                    @if($errors->has('permissions'))-->
            <!--                        <p class="help-block">-->
            <!--                            {{ $errors->first('permissions') }}-->
            <!--                        </p>-->
            <!--                    @endif-->
            <!--                    <p class="helper-block">-->
            <!--                        {{ trans('cruds.role.fields.permissions_helper') }}-->
            <!--                    </p>-->

            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--        <div class="col-md-1 offset-3 form-group">-->
            <!--            <input type="submit" name="submit" value="Send" class="btn btn-success">-->
            <!--        </div>-->
            <!--    </form>-->
            <!--</div>-->
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
                        <th>
                            Action
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
                                {{ $i++ }}
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
                             
                            <td>
                                @if($profile->payment_status == '1')
                                    <button class="btn btn-success">Payment Complete</button>
                                @else
                                    <button class="btn btn-info" style="float: left;margin-right: 5px">Pending</button>
                                    <form action="{{ route('payNow') }}" method="post" style="width: 50px;float: left">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $profile->user_id }}">
                                        <input  class="btn btn-danger" type="submit" value="Pay Now">
                                    </form>
                                @endif
                            </td>
                        
                             
                             
                            <td>
                                @can('profile_edit')
                                    <a href="{{ route('edit-profile',['id' => $profile->id ]) }}" class="btn btn-primary">Edit</a>
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
