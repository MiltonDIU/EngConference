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

        @if(auth()->user()->roles->contains('id', 3))
            @php
                $myProfile = $profiles->where('user_id', auth()->id())->first();
            @endphp

            @if($myProfile && $myProfile->payment_status == '0' && !$myProfile->is_author)
                <div class="card mb-4 border-info shadow-sm" style="border-width: 2px;">
                    <div class="card-body bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="text-info font-weight-bold mb-2">
                                    <i class="fas fa-user-check mr-2"></i> Registration Review & Pay
                                </h4>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-secondary mr-2" style="font-size: 0.9rem;">
                                        {{ $myProfile->is_author ? 'Author Registration' : 'Participant Only Registration' }}
                                    </span>
                                    <span class="text-muted"><i class="fas fa-info-circle mr-1"></i> Your registration is currently pending payment.</span>
                                </div>
                                <h5 class="mb-0 text-dark">Amount Due: <strong>{{ $myProfile->currency ?? 'BDT' }} {{ number_format($myProfile->pay_amount, 2) }}</strong></h5>
                            </div>
                            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                <button class="btn btn-info btn-lg px-4 shadow-sm" data-toggle="modal" data-target="#registrationPayModal" style="border-radius: 8px;">
                                    <i class="fas fa-credit-card mr-2"></i> Review & Pay Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Payment Modal -->
                <div class="modal fade" id="registrationPayModal" tabindex="-1" role="dialog" aria-labelledby="registrationPayModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title font-weight-bold text-dark" id="registrationPayModalLabel">Registration Summary</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body pt-3 pb-4">
                                <div class="bg-light p-4 rounded border mb-4">
                                    <div class="text-center mb-4">
                                        <div class="text-muted small text-uppercase font-weight-bold">Status</div>
                                        <div class="badge badge-warning px-3 py-2" style="border-radius: 20px;">Unpaid</div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Registration Type:</span>
                                        <span class="font-weight-bold text-dark">{{ $myProfile->is_author ? 'Author' : 'Participant Only' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Country:</span>
                                        <span class="font-weight-bold text-dark">{{ $myProfile->country->name ?? 'International' }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-baseline mt-3">
                                        <span class="h5 text-dark">Registration Fee:</span>
                                        <span class="h4 font-weight-bold text-info">{{ $myProfile->currency ?? 'BDT' }} {{ number_format($myProfile->pay_amount, 2) }}</span>
                                    </div>
                                </div>

                                <form action="{{ route('payNow') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $myProfile->user_id }}">
                                    @if($settings['special_discount_is_true']=='true' && $myProfile->coupon_code==null && $myProfile->special_coupon=='REGSP300')
                                        <input type="hidden" name="special_discount" value="REGSP300">
                                    @endif
                                    <button type="submit" class="btn btn-info btn-block btn-lg shadow-sm" style="border-radius: 8px;">
                                        <i class="fas fa-lock mr-2"></i> Proceed to Secure Checkout
                                    </button>
                                </form>
                                <p class="text-center mt-3 small text-muted">
                                    <i class="fas fa-shield-alt mr-1"></i> Your payment is secured via OneCard
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $unpaidPapers = \App\Models\Paper::where('user_id', auth()->id())
                    ->where('status', 'approved')
                    ->where(function($q) {
                        $q->whereNull('payment_status')
                          ->orWhere('payment_status', '!=', '1');
                    })->get();
            @endphp
            @if($unpaidPapers->count() > 0)
                <div class="card mb-4 border-primary shadow-sm" style="border-width: 2px;">
                    <div class="card-body bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-primary font-weight-bold mb-1"><i class="fas fa-shopping-cart mr-2"></i> Abstract Bulk Checkout</h4>
                                <p class="text-muted mb-0">You have <strong>{{ $unpaidPapers->count() }}</strong> approved abstract(s) pending payment. You can pay for all of them securely through a single transaction.</p>
                            </div>
                            <button class="btn btn-primary btn-lg px-4 shadow-sm" data-toggle="modal" data-target="#bulkPaymentModal" style="border-radius: 8px;">
                                <i class="fas fa-credit-card mr-2"></i> Review & Pay All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bulk Payment Review Modal -->
                <div class="modal fade" id="bulkPaymentModal" tabindex="-1" role="dialog" aria-labelledby="bulkPaymentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title font-weight-bold text-dark" id="bulkPaymentModalLabel">Bulk Payment Review</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body pt-3 pb-4">
                                <p class="text-muted mb-4 small">Review the payment details for your approved abstracts before proceeding to checkout.</p>

                                <div class="bg-light p-3 rounded mb-4 border">
                                    <table class="table table-borderless table-sm mb-0">
                                        <thead class="text-muted border-bottom">
                                            <tr>
                                                <th>Abstract ID</th>
                                                <th>Base Rate</th>
                                                <th class="text-right">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalPrice = 0; @endphp
                                            @foreach($unpaidPapers as $up)
                                                @php
                                                    $pricing = \App\Services\PricingService::calculatePaperCost(auth()->user()->profile, $up);
                                                @endphp
                                                <tr>
                                                    <td class="font-weight-bold">{{ $up->submission_id }} <small class="text-muted">({{ $pricing['authors_count'] }} author{{ $pricing['authors_count'] > 1 ? 's' : '' }})</small></td>
                                                    <td>{{ ucfirst($pricing['stage']) }} Price @if($pricing['discount'] > 0)<br><small class="text-success">-{{ $pricing['currency'] }} {{ number_format($pricing['individual_discount'], 2) }} discount per author</small>@endif</td>
                                                    <td class="text-right">{{ $pricing['currency'] }} {{ number_format($pricing['final_price'], 2) }}</td>
                                                </tr>
                                                @if($pricing['authors_count'] > 1)
                                                <tr class="bg-light">
                                                    <td colspan="3" class="py-2 px-4 shadow-sm border-0" style="border-radius: 8px;">
                                                        <div class="mb-2">
                                                            <span class="text-muted font-weight-bold d-block mb-1" style="font-size: 0.85rem;">Authors Details:</span>
                                                            <ul class="mb-0 small text-dark pl-3" style="line-height: 1.4;">
                                                                @foreach($up->authors as $author)
                                                                    <li>{{ $author->name }} @if($author->designation)<span class="text-muted">({{ $author->designation }})</span>@endif</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        <div class="alert alert-success py-1 px-2 m-0 small d-inline-block border-0" style="background-color: #e8f5e9; border-radius: 4px;">
                                                            <i class="fas fa-check-circle mr-1 text-success"></i>
                                                            Rate is <strong>{{ $pricing['currency'] }} {{ number_format($pricing['individual_final_price'], 2) }}</strong> per person.
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endif
                                                @php $totalPrice += $pricing['final_price']; @endphp
                                            @endforeach
                                            <tr class="border-top">
                                                <td colspan="2" class="font-weight-bold text-dark" style="font-size: 1.1rem; padding-top: 15px;">Total Amount</td>
                                                <td class="font-weight-bold text-primary text-right" style="font-size: 1.25rem; padding-top: 15px;">{{ $pricing['currency'] }} {{ number_format($totalPrice, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <form action="{{ route('payNowPapers') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                    @foreach($unpaidPapers as $up)
                                        <input type="hidden" name="paper_ids[]" value="{{ $up->id }}">
                                    @endforeach
                                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm" style="border-radius: 8px;">
                                        <i class="fas fa-lock mr-2"></i> Proceed to Secure Checkout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <div class="card-header">
            Profile
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Speaker">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Reg. ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Professional info</th>
                        <th>Country</th>
                        <th>Mode</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($profiles as $key => $profile)
                        @if($profile->user)
                        <tr data-entry-id="{{ $profile->id }}">
                            <td></td>
                            <td class="font-weight-bold text-dark">
                                @if($profile->payment_status == 1 && $profile->registration_id == null)
                                    <a href="{{ route('generateIds', [$profile->id]) }}" class="btn btn-xs btn-outline-primary">
                                        Generate ID
                                    </a>
                                @else
                                    {{ $profile->registration_id ?? 'N/A' }}
                                @endif
                            </td>
                            <td>
                                {{ $profile->user->name ?? '' }}
                                <div class="mt-1">
                                    @if($profile->is_author)
                                        <span class="badge badge-primary px-2" style="font-size: 0.7rem;">Author</span>
                                    @else
                                        <span class="badge badge-info px-2" style="font-size: 0.7rem;">Participant Only</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="fas fa-envelope text-muted mr-1"></i> {{ $profile->user->email ?? '' }}<br>
                                    <i class="fab fa-whatsapp text-success mr-1"></i> {{ $profile->whatsapp_number ?? '' }}
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <strong>{{ $profile->designation ?? 'N/A' }}</strong><br>
                                    {{ $profile->department ?? '' }} @if($profile->department && $profile->institution)<br>@endif
                                    <span class="text-muted">{{ $profile->institution ?? '' }}</span>
                                </div>
                            </td>
                            <td>
                                {{ $profile->country->name ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $profile->participation_mode == 'onsite' ? 'success' : 'warning' }} px-2 py-1">
                                    {{ ucfirst($profile->participation_mode ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="small">
                                {{ date('d M, Y', strtotime($profile->created_at)) }}
                            </td>
                            <td class="font-weight-bold">
                                {{ $profile->currency ?? 'BDT' }} {{ number_format($profile->pay_amount ?? 0, 2) }}
                            </td>
                            <td>
                                @if($profile->payment_status == '1')
                                    <div class="alert alert-success d-inline-block py-1 px-3 mb-0" style="border-radius: 20px;">
                                        <i class="fas fa-check-circle mr-1"></i> Paid
                                    </div>
                                @else
                                    <div class="alert alert-warning d-inline-block py-1 px-3 mb-0" style="border-radius: 20px;">
                                        <i class="fas fa-clock mr-1"></i> Unpaid
                                    </div>
                                    @if(!auth()->user()->roles->contains(3))
                                        <div class="mt-2">
                                            <form action="{{ route('onecard.verify') }}" method="POST" onsubmit="return confirm('Verify all transaction attempts for this user with OneCard?')">
                                                @csrf
                                                <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                                                <button type="submit" class="btn btn-xs btn-outline-info shadow-sm" title="Check OneCard for all transaction attempts">
                                                    <i class="fas fa-sync-alt mr-1"></i> Verify OneCard
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    @can('profile_edit')
                                        <a href="{{ route('edit-profile',['id' => $profile->id ]) }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan

                                    @if(auth()->id() == $profile->user_id && $profile->is_author && !$profile->user->paper && ($settings['is_abstract_submission_open'] ?? 'true') == 'true')
                                        <a href="{{ route('papers.submit') }}" class="btn btn-xs btn-warning">Submit Abstract</a>
                                    @endif
                                </div>

                                @if($profile->user && $profile->user->paper)
                                    <div class="mt-1">
                                        <span class="badge badge-dark">Submitted: {{ $profile->user->paper->submission_id }}</span>
                                    </div>
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
