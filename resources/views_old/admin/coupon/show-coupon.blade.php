@extends('layouts.admin')
@section('content')
    @can('events_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route("admin.coupon.create") }}">
                    Add Coupon
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            Coupons
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-events">
                    <thead>
                    <tr>
                        <th>

                        </th>
                        <th>
                            Title
                        </th>
                        <th>
                            Email
                        </th>
                        <th>
                            Value
                        </th>
                        <th>
                            User
                        </th>
                        <th>
                            Referral Link
                        </th>
                        <th>
                           Total Registration
                        </th>
                        <th>
                          Paid Registration
                        </th>
                        <th>
                            Is DEN User Only
                        </th>
                        <th>
                            Use Status
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($coupons as $key => $coupon)
                        <tr data-entry-id="{{ $coupon->id }}">

                            <td>
                                {{ $coupon->id ?? '' }}
                            </td>
                            <td>
                                {{ $coupon->title ?? '' }}
                            </td>
                            <td>
                                {{ $coupon->email ?? '' }}
                            </td>
                            <td>
                                {{ $coupon->value ?? '' }}
                            </td>
                            <td>
                                {{ $coupon->user->name ?? '' }}
                            </td>

                            <td>
                                {{ $coupon->referral!=null ? $coupon->referral->name:'No Referral Link' }}
                            </td>

                            <td>
                             {{ \App\Models\Coupon::countProfile($coupon->title) }}
                            </td>

                            <td>
                                {{ \App\Models\Coupon::countProfilePaid($coupon->title) }}
                            </td>
                            <td>
                              {{ $coupon->is_domain == '0' ?'No':'Yes' }}
                            </td>
                            <td>
                                {{ $coupon->use_status == '0' ?'Active':'Used' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.coupon.edit',$coupon->id) }}" class="btn btn-primary btn-sm">Edit</a>
{{--                                <a href="{{ route('admin.coupon.destroy',$coupon->id) }}" class="btn btn-danger btn-sm">Delete</a>--}}
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
