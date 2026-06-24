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
                $paymentLastDate = isset($settings['payment_last_date']) ? \Illuminate\Support\Carbon::parse($settings['payment_last_date']) : null;
                $isPaymentOpen = !$paymentLastDate || \Illuminate\Support\Carbon::now()->lte($paymentLastDate);
            @endphp

            @if($myProfile && $myProfile->payment_status != '1' && !$myProfile->is_author)
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
                                @if($isPaymentOpen)
                                    <form action="{{ route('profile.recalculate-fee') }}" method="POST" class="d-inline-block mr-2">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-info btn-lg px-3" style="border-radius: 8px;" title="Refresh and update payment amount according to current timeline stage">
                                            <i class="fas fa-sync-alt mr-1"></i> Refresh Price
                                        </button>
                                    </form>
                                    <button class="btn btn-info btn-lg px-4 shadow-sm" data-toggle="modal" data-target="#registrationPayModal" style="border-radius: 8px;">
                                        <i class="fas fa-credit-card mr-2"></i> Review & Pay Now
                                    </button>
                                @else
                                    <span class="badge badge-danger p-3 font-weight-bold" style="font-size: 1rem; border-radius: 8px;">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Payment Closed
                                    </span>
                                @endif
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
                @if(!$myProfile->author_list_confirmed)
                    <div class="card mb-4 border-warning shadow-sm" style="border-width: 2px; border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-warning text-dark py-3">
                            <h5 class="card-title font-weight-bold mb-0">
                                <i class="fas fa-id-card mr-2"></i> Confirm Author List & Student Status
                            </h5>
                        </div>
                        <div class="card-body bg-white p-4">
                            <p class="text-muted mb-4">
                                Please select your student status (yes/no) very carefully, as this option can be chosen only once and cannot be changed later.   </p>

                            <form action="{{ route('profile.confirm-student-status') }}" method="POST">
                                @csrf

                                @foreach($unpaidPapers as $paper)
                                    <div class="mb-4 p-3 bg-light rounded border">
                                        <h6 class="font-weight-bold text-primary mb-3">
                                            <i class="fas fa-file-alt mr-1"></i> Paper ID: {{ $paper->submission_id }} - {{ $paper->title }}
                                        </h6>

                                        <table class="table table-sm table-bordered bg-white mb-0">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th>Author Name</th>
                                                    <th>Email</th>
                                                    <th>Designation</th>
                                                    <th>Country</th>
                                                    <th style="width: 180px;" class="text-center">Is Student?</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($paper->authors as $author)
                                                    <tr>
                                                        <td class="align-middle font-weight-bold">{{ $author->name }}</td>
                                                        <td class="align-middle">{{ $author->email }}</td>
                                                        <td class="align-middle">{{ $author->designation }}</td>
                                                        <td class="align-middle">{{ $author->country->name ?? 'N/A' }}</td>
                                                        <td class="align-middle text-center">
                                                            <input type="hidden" name="authors[{{ $author->id }}][id]" value="{{ $author->id }}">
                                                            <div class="student-status-toggle">
                                                                <input type="radio" id="student_yes_{{ $author->id }}" name="authors[{{ $author->id }}][is_student]" value="1" {{ $author->is_student ? 'checked' : '' }} required>
                                                                <label for="student_yes_{{ $author->id }}" class="toggle-btn toggle-yes">Yes</label>

                                                                <input type="radio" id="student_no_{{ $author->id }}" name="authors[{{ $author->id }}][is_student]" value="0" {{ !$author->is_student ? 'checked' : '' }} required>
                                                                <label for="student_no_{{ $author->id }}" class="toggle-btn toggle-no">No</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach

                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-warning btn-lg font-weight-bold shadow-sm">
                                        <i class="fas fa-check-circle mr-1"></i> Confirm & Proceed to Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card mb-4 border-primary shadow-sm" style="border-width: 2px;">
                        <div class="card-body bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="text-primary font-weight-bold mb-1"><i class="fas fa-shopping-cart mr-2"></i> Abstract Bulk Checkout</h4>
                                    <p class="text-muted mb-0">You have <strong>{{ $unpaidPapers->count() }}</strong> approved abstract(s) pending payment. You can pay for all of them securely through a single transaction.</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    @if($isPaymentOpen)
                                        <form action="{{ route('profile.recalculate-fee') }}" method="POST" class="d-inline-block mr-2">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary btn-lg px-3" style="border-radius: 8px;" title="Refresh and update payment amount according to current timeline stage">
                                                <i class="fas fa-sync-alt mr-1"></i> Refresh Price
                                            </button>
                                        </form>
                                        <button class="btn btn-primary btn-lg px-4 shadow-sm" data-toggle="modal" data-target="#bulkPaymentModal" style="border-radius: 8px;">
                                            <i class="fas fa-credit-card mr-2"></i> Review & Pay All
                                        </button>
                                    @else
                                        <span class="badge badge-danger p-3 font-weight-bold" style="font-size: 1rem; border-radius: 8px;">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Payment Closed
                                        </span>
                                    @endif
                                </div>
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
                                                                         @php
                                                                             $authorFee = $pricing['author_fees'][$author->id] ?? $pricing['individual_final_price'];
                                                                         @endphp
                                                                         <li>
                                                                             {{ $author->name }} 
                                                                             @if($author->designation)<span class="text-muted">({{ $author->designation }})</span>@endif 
                                                                             - <strong class="text-primary">{{ $pricing['currency'] }} {{ number_format($authorFee, 2) }}</strong>
                                                                         </li>
                                                                     @endforeach
                                                                 </ul>
                                                             </div>
                                                             @php
                                                                 $uniqueFees = array_unique($pricing['author_fees'] ?? []);
                                                                 $hasDifferentFees = count($uniqueFees) > 1;
                                                             @endphp
                                                             @if($hasDifferentFees)
                                                                 <div class="alert alert-info py-1 px-2 m-0 small d-inline-block border-0" style="background-color: #e3f2fd; color: #0d47a1; border-radius: 4px;">
                                                                     <i class="fas fa-info-circle mr-1"></i>
                                                                     Individual rates apply based on student/regular status.
                                                                 </div>
                                                             @else
                                                                 <div class="alert alert-success py-1 px-2 m-0 small d-inline-block border-0" style="background-color: #e8f5e9; border-radius: 4px;">
                                                                     <i class="fas fa-check-circle mr-1 text-success"></i>
                                                                     Rate is <strong>{{ $pricing['currency'] }} {{ number_format($pricing['individual_final_price'], 2) }}</strong> per person.
                                                                 </div>
                                                             @endif
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
        @endif

        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Profile</span>
            @if(!auth()->user()->roles->contains(3))
                <form action="{{ route('profile.recalculate-all-unpaid') }}" method="POST" onsubmit="return confirm('Recalculate fees for ALL unpaid users based on current settings timeline?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm">
                        <i class="fas fa-sync-alt mr-1"></i> Recalculate All Unpaid Fees
                    </button>
                </form>
            @endif
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter_registration_type" class="small font-weight-bold text-muted mb-1">Filter by Registration Type</label>
                    <select id="filter_registration_type" class="form-control form-control-sm">
                        <option value="">All Types</option>
                        <option value="author">Author</option>
                        <option value="participant">Participant Only</option>
                    </select>
                </div>
            </div>
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
                        <th>Authors</th>
                        <th>Total Member</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($profiles as $key => $profile)
                        @if($profile->user)
                        <tr data-entry-id="{{ $profile->id }}" data-registration-type="{{ $profile->is_author ? 'author' : 'participant' }}">
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
                                @elseif($profile->payment_status == '2')
                                    <div class="alert alert-info d-inline-block py-1 px-3 mb-0" style="border-radius: 20px;">
                                        <i class="fas fa-hourglass-half mr-1"></i> Partially Paid
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
                                @if($profile->user && $profile->user->papers->count() > 0)
                                    @foreach($profile->user->papers as $paper)
                                        <div class="small mb-2">
                                            @can('paper_show')
                                                <a href="{{ route('papers.show', $paper->id) }}" class="font-weight-bold text-primary" title="View Abstract Details">
                                                    {{ $paper->submission_id }}:
                                                </a>
                                            @else
                                                <strong class="text-primary">{{ $paper->submission_id }}:</strong>
                                            @endcan
                                            <br>
                                            <span class="text-dark">{{ $paper->authors->pluck('name')->implode(', ') }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="text-center font-weight-bold">
                                @php
                                    $totalMembers = 0;
                                    if ($profile->user && $profile->user->papers->count() > 0) {
                                        foreach ($profile->user->papers as $paper) {
                                            $totalMembers += max(1, $paper->authors->count());
                                        }
                                    } else {
                                        $totalMembers = 1;
                                    }
                                @endphp
                                {{ $totalMembers }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    @can('profile_edit')
                                        <a href="{{ route('edit-profile',['id' => $profile->id ]) }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan

                                    @if($profile->payment_status != '1' && !auth()->user()->roles->contains(3))
                                        <form action="{{ route('profile.recalculate-fee') }}" method="POST" class="d-inline-block ml-1">
                                            @csrf
                                            <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                                            <button type="submit" class="btn btn-xs btn-outline-secondary" onclick="return confirm('Recalculate registration fee for this user?')">
                                                <i class="fas fa-sync-alt"></i> Recalculate
                                            </button>
                                        </form>
                                    @endif
                                </div>
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

            // Clean up raw text, collapse extra whitespace/newlines, exclude button/form text
            let exportFormat = {
                body: function (data, row, column, node) {
                    let $cell = $(node).clone();
                    // Remove forms, buttons, or hidden/action elements if they exist
                    $cell.find('form, button, .btn, script, style').remove();

                    let rawText = $cell.text() || "";
                    let lines = rawText.split('\n')
                        .map(line => line.trim())
                        .filter(line => line.length > 0);
                    return lines.join(', ');
                }
            };

            // Apply custom export format to all export buttons
            dtButtons.forEach(function(btn) {
                if (btn.extend === 'copy' || btn.extend === 'csv' || btn.extend === 'excel' || btn.extend === 'pdf' || btn.extend === 'print') {
                    if (!btn.exportOptions) {
                        btn.exportOptions = {};
                    }
                    btn.exportOptions.format = exportFormat;
                }
            });

            let table = $('.datatable-Speaker:not(.ajaxTable)').DataTable({
                buttons: dtButtons,
                order: [[ 1, 'desc' ]],
                pageLength: 100,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
            });

            // Register a custom search filter for Registration Type
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    if (!$(settings.nTable).hasClass('datatable-Speaker')) {
                        return true;
                    }
                    let selectedType = $('#filter_registration_type').val();
                    if (!selectedType) {
                        return true; // No filter selected
                    }
                    let row = table.row(dataIndex).node();
                    let rowType = $(row).attr('data-registration-type');
                    return rowType === selectedType;
                }
            );

            // Redraw table when filter changes
            $('#filter_registration_type').on('change', function() {
                table.draw();
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })

    </script>
@endsection

@push('style')
<style>
    .student-status-toggle {
        display: inline-flex;
        border: 1px solid #ced4da;
        border-radius: 20px;
        overflow: hidden;
        background-color: #f8f9fa;
        padding: 2px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }
    .student-status-toggle input[type="radio"] {
        display: none;
    }
    .student-status-toggle .toggle-btn {
        padding: 4px 16px;
        margin: 0;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        border-radius: 18px;
        transition: all 0.2s ease-in-out;
        color: #6c757d;
        text-align: center;
        user-select: none;
    }
    .student-status-toggle .toggle-btn:hover {
        color: #495057;
        background-color: #e9ecef;
    }
    .student-status-toggle input[type="radio"]:checked + .toggle-yes {
        background-color: #28a745;
        color: #fff;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
    }
    .student-status-toggle input[type="radio"]:checked + .toggle-no {
        background-color: #6c757d;
        color: #fff;
        box-shadow: 0 2px 4px rgba(108, 117, 125, 0.2);
    }
</style>
@endpush
