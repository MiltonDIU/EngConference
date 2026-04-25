@extends('layouts.admin')
@section('content')
<div class="card">
<div class="card-header">
Dashboard
</div>

<div class="card-body">
<div class="content">
@can('admin_report')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="info-box shadow-none border bg-white" style="border-radius: 12px; border-left: 5px solid #6f42c1 !important;">
            <span class="info-box-icon text-purple"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text font-weight-bold text-muted small uppercase">Total Registration</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $total }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box shadow-none border bg-white" style="border-radius: 12px; border-left: 5px solid #007bff !important;">
            <span class="info-box-icon text-primary"><i class="fas fa-user-edit"></i></span>
            <div class="info-box-content">
                <span class="info-box-text font-weight-bold text-muted small uppercase">Total Authors</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $totalAuthors }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box shadow-none border bg-white" style="border-radius: 12px; border-left: 5px solid #28a745 !important;">
            <span class="info-box-icon text-success"><i class="fas fa-walking"></i></span>
            <div class="info-box-content">
                <span class="info-box-text font-weight-bold text-muted small uppercase">Participants Only</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $totalParticipants }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="info-box shadow-none border bg-light" style="border-radius: 12px;">
            <span class="info-box-icon text-primary"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text font-weight-bold text-muted small uppercase">Total Abstracts</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $totalPapers }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box shadow-none border bg-light" style="border-radius: 12px;">
            <span class="info-box-icon text-warning"><i class="fas fa-hourglass-half"></i></span>
            <div class="info-box-content">
                <span class="info-box-text font-weight-bold text-muted small uppercase">Pending Review</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $pendingPapers }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box shadow-none border bg-light" style="border-radius: 12px;">
            <span class="info-box-icon text-success"><i class="fas fa-wallet"></i></span>
            <div class="info-box-content">
                <span class="info-box-text font-weight-bold text-muted small uppercase">Paid Papers</span>
                <span class="info-box-number h4 mb-0 font-weight-bold">{{ $paidPapers }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">Abstract Status Overview</h5>
            </div>
            <div class="card-body">
                <div id="chart_paper_status" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">Approved Abstract Payments</h5>
            </div>
            <div class="card-body">
                <div id="chart_paper_payment" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">

<div class="col-lg-6">
<div class="card">
<div class="card-header">
Total Registered Users
</div>

<div class="card-body">
<div id="chartdiv"></div>
</div>
</div>
</div>
<div class="col-lg-6">
    <div class="card shadow-sm border-0" style="border-radius: 12px; height: 100%;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title font-weight-bold mb-0 text-dark"><i class="fas fa-file-invoice-dollar mr-2 text-success"></i> Financial Overview by Registration Type</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="min-width: 600px;">
                    <thead class="bg-light small uppercase font-weight-bold text-muted">
                        <tr>
                            <th class="border-0 px-4 py-3">Currency</th>
                            <th class="border-0 py-3 text-center">Authors<br><small class="text-xs">(Paid / Unpaid)</small></th>
                            <th class="border-0 py-3 text-center">Participants<br><small class="text-xs">(Paid / Unpaid)</small></th>
                            <th class="border-0 px-4 py-3 text-right">Total<br><small class="text-xs">(Paid / Unpaid)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($currencyStats as $stat)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-weight-bold h6 mb-0 text-dark-blue">{{ $stat->currency }}</div>
                                    <small class="text-muted"><i class="fas fa-users mr-1"></i> {{ $stat->total_users }} Users</small>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="text-success font-weight-bold small"><i class="fas fa-check-circle mr-1 opacity-7"></i> {{ number_format($stat->author_paid_amt, 0) }}</div>
                                    <div class="text-danger font-weight-bold small"><i class="fas fa-clock mr-1 opacity-7"></i> {{ number_format($stat->author_unpaid_amt, 0) }}</div>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="text-success font-weight-bold small"><i class="fas fa-check-circle mr-1 opacity-7"></i> {{ number_format($stat->participant_paid_amt, 0) }}</div>
                                    <div class="text-danger font-weight-bold small"><i class="fas fa-clock mr-1 opacity-7"></i> {{ number_format($stat->participant_unpaid_amt, 0) }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="text-success font-weight-bold h6 mb-0"><strong>{{ number_format($stat->paid_amount, 0) }}</strong></div>
                                    <div class="text-danger font-weight-bold small"><strong>{{ number_format($stat->unpaid_amount, 0) }}</strong></div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-info-circle mr-1"></i> No currency-specific data found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="col-lg-12">
    <div class="card shadow-sm border-0 mt-4" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title font-weight-bold mb-0 text-dark">
                <i class="fas fa-chart-bar mr-2 text-primary"></i> Top Submission Tracks
            </h5>
        </div>
        <div class="card-body py-4">
            <div class="row">
                @forelse($topTracks as $track)
                    @php
                        $percentage = $totalPapers > 0 ? ($track->submission_count / $totalPapers) * 100 : 0;
                        $barColor = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf'][$loop->index % 10];
                    @endphp
                    <div class="col-md-6 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold text-dark text-truncate" style="max-width: 70%;" title="{{ $track->name }}">
                                {{ $track->name }}
                            </span>
                            <span class="badge badge-light border text-primary px-2">{{ $track->submission_count }} Total</span>
                        </div>
                        <div class="progress mb-2" style="height: 10px; border-radius: 10px; background-color: #f8f9fc;">
                            <div class="progress-bar" role="progressbar"
                                style="width: {{ $percentage }}%; background-color: {{ $barColor }}; border-radius: 10px;"
                                aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <!-- Status Breakdown Mini-Bars -->
                        <div class="row no-gutters mt-2">
                            <div class="col-4 pr-1">
                                <div class="d-flex justify-content-between x-small mb-1">
                                    <span class="text-muted">Approved</span>
                                    <span class="font-weight-bold text-success">{{ $track->approved_count }}</span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $track->submission_count > 0 ? ($track->approved_count / $track->submission_count) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div class="col-4 px-1">
                                <div class="d-flex justify-content-between x-small mb-1">
                                    <span class="text-muted">Pending</span>
                                    <span class="font-weight-bold text-warning">{{ $track->pending_count }}</span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $track->submission_count > 0 ? ($track->pending_count / $track->submission_count) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <div class="col-4 pl-1">
                                <div class="d-flex justify-content-between x-small mb-1">
                                    <span class="text-muted">Rejected</span>
                                    <span class="font-weight-bold text-danger">{{ $track->rejected_count }}</span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $track->submission_count > 0 ? ($track->rejected_count / $track->submission_count) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="fas fa-folder-open mb-2 h2 d-block opacity-2"></i>
                        No submissions recorded yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>
@endcan

@auth
@if (auth()->user()->roles->contains('id', 3))
<div class="row">
<div class="col-md-12">
@if(auth()->user()->profile->payment_status=='1')
<div class="alert alert-success alert-dismissible fade show" style="font-size: 20px">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<strong>Success!</strong>
We are pleased to inform you that your payment has been <strong>successfully</strong> processed, and we are excited to let you know that your<strong> seat is confirmed!</strong><br><strong style="font-size: 30px">
    @php
        $identity = auth()->user()->profile->identity_no;
        if ($identity != null){
             echo  "Your Registration ID is : ".substr($identity, 6, 4);
        }
    @endphp
    @if(auth()->user()->profile->payment_status=='1' && auth()->user()->profile->identity_no==null)
        <a href="{{ route('generateIds') }}" class="btn btn-primary">Click Here</a> to get your Registration ID
    @endif
</strong>
</div>
@else
                    <div class="alert alert-info alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Info!</strong> We have received your registration details; however, it appears that the payment for your account has not been completed yet. <strong>Please note that your seat is not confirmed until the payment is processed successfully</strong>
                    </div>
                @endif

                @if($unpaidPapers->count() > 0)
                    <div class="card mb-4 border-primary shadow-sm" style="border-width: 2px; border-radius: 12px; overflow: hidden;">
                        <div class="card-body bg-light p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="mb-3 mb-md-0">
                                    <h4 class="text-primary font-weight-bold mb-2"><i class="fas fa-shopping-cart mr-2"></i> Abstract Bulk Checkout</h4>
                                    <p class="text-muted mb-0" style="font-size: 1.1rem;">You have <strong>{{ $unpaidPapers->count() }}</strong> approved abstract(s) pending payment. You can pay for all of them securely through a single transaction.</p>
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
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                                <div class="modal-header bg-primary text-white border-0 py-3">
                                    <h5 class="modal-title font-weight-bold" id="bulkPaymentModalLabel">Bulk Payment Review</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-4 bg-white">
                                    <p class="text-muted mb-4 small font-italic">Review the payment details for your approved abstracts before proceeding to secure checkout.</p>

                                    <div class="bg-light p-3 rounded mb-4 border shadow-sm">
                                        <table class="table table-borderless table-sm mb-0">
                                            <thead class="text-muted border-bottom small uppercase font-weight-bold">
                                                <tr>
                                                    <th class="pb-2">Abstract ID</th>
                                                    <th class="pb-2">Rate Breakdown</th>
                                                    <th class="pb-2 text-right">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalPrice = 0; @endphp
                                                @foreach($unpaidPapers as $up)
                                                    @php
                                                        $pricing = \App\Services\PricingService::calculatePaperCost(auth()->user()->profile, $up);
                                                    @endphp
                                                    <tr class="border-bottom">
                                                        <td class="py-3 font-weight-bold text-dark">{{ $up->submission_id }} <small class="text-muted">({{ $pricing['authors_count'] }} author{{ $pricing['authors_count'] > 1 ? 's' : '' }})</small></td>
                                                        <td class="py-3 align-middle"><span class="badge badge-info">{{ ucfirst($pricing['stage']) }} Price</span></td>
                                                        <td class="py-3 text-right font-weight-bold text-dark">{{ $pricing['currency'] }} {{ number_format($pricing['final_price'], 2) }}</td>
                                                    </tr>
                                                    @php $totalPrice += $pricing['final_price']; @endphp
                                                @endforeach
                                                <tr>
                                                    <td colspan="2" class="pt-4 font-weight-bold text-uppercase" style="font-size: 1.1rem;">Total Amount</td>
                                                    <td class="pt-4 font-weight-bold text-primary text-right" style="font-size: 1.5rem;">{{ $pricing['currency'] }} {{ number_format($totalPrice, 2) }}</td>
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
                                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm py-3" style="border-radius: 8px;">
                                            <i class="fas fa-lock mr-2"></i> Proceed to Secure Checkout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


{{--<h2 style="color:red">--}}
{{--@if(auth()->user()->profile->payment_status=='1')--}}
{{--<strong>You paid: {{ auth()->user()->profile->pay_amount??"" }}, Thank you for your payment</strong>--}}
{{--@else--}}
{{--Your payable amount: {{ auth()->user()->profile->pay_amount??"" }}--}}
{{--@endif--}}

{{--{{  auth()->user()->profile->coupon_code!=null?" and You are used coupon code: " .auth()->user()->profile->coupon_code:""}}</h2>--}}
{{--    --}}
{{--    --}}
{{--<div class="row" style="margin: 50px 0px">--}}
{{--<div class="col-md-1">--}}
{{--   <strong> Payment Status:</strong>--}}
{{--</div>--}}
{{--<div class="col-md-11">--}}
{{--    @if(auth()->user()->profile->payment_status == '1')--}}
{{--        <button class="btn btn-success">Payment Complete</button>--}}
{{--    @else--}}

{{--        @if($settings['seat_is_full']=='false')--}}
{{--            <button class="btn btn-info" style="float: left;margin-right: 5px">Pending</button>--}}

{{--            @php--}}
{{--                $domain = explode('@', auth()->user()->profile->user->email);--}}
{{--            @endphp--}}

{{--            @if($settings['special_discount_is_true']=='true' and auth()->user()->profile->coupon_code==null and auth()->user()->profile->special_coupon=='REGSP300' and (in_array($domain[1], $allowedDomain)!=true) )--}}
{{--                <form action="{{ route('payNow') }}" method="post" style="width: 50px;float: left;">--}}
{{--                    @csrf--}}
{{--                    <input type="hidden" name="user_id" value="{{ auth()->user()->profile->user_id }}">--}}
{{--                    <input type="hidden" name="special_discount" value="REGSP300">--}}
{{--                    <input  class="btn btn-danger" type="submit" value="Pay With Coupon extra {{ $settings['special_discount']??"0" }}% (REGSP300)">--}}
{{--                </form>--}}
{{--            @else--}}
{{--                <form action="{{ route('payNow') }}" method="post" style="width: 50px;float: left">--}}
{{--                    @csrf--}}
{{--                    <input type="hidden" name="user_id" value="{{ auth()->user()->profile->user_id }}">--}}
{{--                    <input  class="btn btn-danger" type="submit" value="Pay Now">--}}
{{--                </form>--}}

{{--            @endif--}}

{{--        @else--}}
{{--            <button class="btn btn-info" style="float: left;margin-right: 5px">Seat is Full</button>--}}
{{--        @endcan--}}
{{--    @endif--}}
{{--</div>--}}


{{--</div>--}}

</div>
{{--
<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            Session Rating
        </div>
        <div class="card-body">
            ... Content Omitted ...
        </div>
    </div>
</div>
--}}



<div class="card">
<div class="card-header">
Participation Benefits
</div>
<div class="card-body">
<table class="table table-bordered">

    <tr>
        <td>
            <ol>
                @foreach($aminities as $key=> $aminity)
                    <li>{{ $aminity->name }}</li>
                @endforeach
            </ol>
        </td>
    </tr>

</table>
</div>
</div>
<div class="card">
<div class="card-header">
Total Program Schedule
</div>
<div class="card-body">
<table class="table table-bordered">
    @foreach($allSchedules as $key=> $schedule)

        <tr>
            <td><strong>Day {{$key}}</strong></td>
            <td>
                @foreach($schedule as $key=> $sc)
                    {{--                                                {{ dd($sc->speaker->speaker_type_id) }}--}}

                    {!! ++$key. '. '.$sc->title!!}
                    <br>
                    Start Time & Duration: {{ \Carbon\Carbon::parse($sc->start_time)->format('h:i A') }} -- {{ $sc->subtitle }}
                    <br>
                @endforeach
            </td>
        </tr>
    @endforeach
</table>

</div>
</div>
</div>

<div class="col-lg-6">
                    {{--
                    <div class="card">
                        <div class="card-header">
                            My Registered Workshop List
                        </div>
                        <div class="card-body">
                            ... Content Omitted ...
                        </div>
                    </div>
                    --}}
<div class="card">
<div class="card-header">
Blogs
</div>
<div class="card-body">
<table class="table table-bordered">

    @foreach( $blogs as $key=> $blog)

        <tr>
            <td>
                <a class="link-muted" href="{{ route('blogDetails',[$blog->id,$blog->slug]) }}" target="_blank">  <strong>
                        {{ $blog->title }}


                    </strong></a>
            </td>
        </tr>
    @endforeach


</table>

</div>
</div>

<div class="card">
<div class="card-header">
Relevant Resource
</div>
<div class="card-body">
<table class="table table-bordered">
    @foreach( $eventActivities as $key=> $eventActivity)
        <tr>
            <td>
                @if($eventActivity->feature_image)
                    <a href="{{ $eventActivity->link??"#" }}" target="_blank" style="float: left;margin-right: 20px">
                        <img src="{{ $eventActivity->feature_image->getUrl() }}" width="200">
                    </a>
                @endif
                <a href="{{ $eventActivity->link??"#" }}" target="_blank" style="color:#230134">
                    <strong>    {!! $eventActivity->title !!}</strong>

                </a>
                <p>
                    {!! $eventActivity->summary !!}
                </p>
            </td>
        </tr>
    @endforeach
</table>
</div>
</div>
</div>
</div>
@endif
@endauth

</div>
</div>
</div>
@endsection

@push('style')
<style>
#chartdiv {
width: 100%;
height: 500px;
}

#chartdiv3 {
width: 100%;
height: 500px;
}
#chart_paper_status, #chart_paper_payment {
    width: 100%;
    height: 300px;
}
.link-muted:hover{color: #7016B6}
</style>



@endpush

@push('script')

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<!-- Chart code -->

<!-- Chart code -->
<script>
var total = {!! json_encode($total) !!};
var profiles = {!! json_encode($profiles) !!};
am4core.ready(function() {
// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end
var chart = am4core.create("chartdiv", am4charts.PieChart3D);
chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
chart.data = profiles;
if (total === 0) {
// Create a new chart with a default value of "No data available"
var chart = am4core.create("totalOverview", am4charts.PieChart3D);
chart.innerRadius = 100;
var label = chart.seriesContainer.createChild(am4core.Label);
label.text = "No data available";
label.horizontalCenter = "middle";
label.verticalCenter = "middle";
label.fontSize = 30;
label.y = -20;
label.multiline = true;
label.textAlign = "center";
}else{
chart.innerRadius = 100;
var label = chart.seriesContainer.createChild(am4core.Label);
label.text = "Total\n{{ $total }}"; // use \n instead of <br>
label.horizontalCenter = "middle";
label.verticalCenter = "middle";
label.fontSize = 30;
label.y = -25; // move label 20 pixels upwards from the center
label.multiline = true; // enable multiline text
label.textAlign = "center"; // center-align the text within the label
}


chart.innerRadius = am4core.percent(40);
chart.depth = 30;
chart.legend = new am4charts.Legend();

var series = chart.series.push(new am4charts.PieSeries3D());
series.dataFields.value = "litres";
series.dataFields.depthValue = "litres";
series.dataFields.category = "country";
series.slices.template.cornerRadius = 3;
series.colors.step = 3;
}); // end am4core.ready()
</script>




<script>
// document.addEventListener('DOMContentLoaded', function() {
//     var labels = document.querySelectorAll('.rating label');
//
//     labels.forEach(function(label) {
//         label.addEventListener('click', function() {
//             var clickedIndex = Array.from(labels).indexOf(label);
//             for (var i = 0; i <= clickedIndex; i++) {
//                 labels[i].style.color = '#ffcc00';
//             }
//             for (var i = clickedIndex + 1; i < labels.length; i++) {
//                 labels[i].style.color = '#ddd';
//             }
//         });
//     });
// });
document.addEventListener('DOMContentLoaded', function() {
var labels = document.querySelectorAll('.rating label');
var defaultCheckedIndex = 4; // Index of the default checked star (5th star)

// Initialize colors based on the default checked index
for (var i = 0; i <= defaultCheckedIndex; i++) {
labels[i].style.color = '#ffcc00';
}

// Event listener for clicking on stars
labels.forEach(function(label, index) {
label.addEventListener('click', function() {
for (var i = 0; i <= index; i++) {
labels[i].style.color = '#ffcc00';
}
for (var i = index + 1; i < labels.length; i++) {
labels[i].style.color = '#ddd';
}
});
});
});



</script>
<script>
    // Replace <br> with line breaks in dropdown options
    document.addEventListener("DOMContentLoaded", function() {
        var select = document.getElementById("schedule_id");
        var options = select.getElementsByTagName("option");
        for (var i = 0; i < options.length; i++) {
            options[i].innerHTML = options[i].innerHTML.replace(/<br>/g, "\n");
        }
    });
</script>

<script>
    am4core.ready(function() {
        am4core.useTheme(am4themes_animated);

        // Paper Status Chart
        var statusChart = am4core.create("chart_paper_status", am4charts.PieChart3D);
        statusChart.hiddenState.properties.opacity = 0;
        statusChart.data = {!! json_encode($paperStats) !!};
        statusChart.innerRadius = am4core.percent(40);
        statusChart.depth = 20;
        statusChart.legend = new am4charts.Legend();

        var statusSeries = statusChart.series.push(new am4charts.PieSeries3D());
        statusSeries.dataFields.value = "litres";
        statusSeries.dataFields.category = "category";
        statusSeries.slices.template.cornerRadius = 5;
        statusSeries.colors.step = 3;

        // Paper Payment Chart
        var paymentChart = am4core.create("chart_paper_payment", am4charts.PieChart3D);
        paymentChart.hiddenState.properties.opacity = 0;
        paymentChart.data = {!! json_encode($paperPaymentStats) !!};
        paymentChart.innerRadius = am4core.percent(40);
        paymentChart.depth = 20;
        paymentChart.legend = new am4charts.Legend();

        var paymentSeries = paymentChart.series.push(new am4charts.PieSeries3D());
        paymentSeries.dataFields.value = "litres";
        paymentSeries.dataFields.category = "category";
        paymentSeries.slices.template.cornerRadius = 5;
        paymentSeries.colors.list = [
            am4core.color("#28a745"),
            am4core.color("#ffc107")
        ];
    });
</script>
@endpush


@push('style')
<style>
/* Star rating styles */
.rating {
display: inline-block;
}

.rating input {
display: none;
}

.rating label {
cursor: pointer;
color: #ddd;
}

.rating label:before {
content: '\2605';
font-size: 24px;
}

.rating input:checked ~ label,
.rating label:hover {
color: #ffcc00;
}

</style>
@endpush
