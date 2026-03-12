@extends('layouts.admin')
@section('content')
<div class="card">
<div class="card-header">
Dashboard
</div>

<div class="card-body">
<div class="content">
@can('admin-dashboard')
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
<div class="card">
<div class="card-header">
Total Paid/Unpaid TK
</div>

<div class="card-body">
<div id="chartdiv3"></div>
</div>
</div>
</div>
<div class="col-lg-6">
<div class="card">
<div class="card-header">
Event Program Schedule
</div>

<div class="card-body">
<table class="table table-bordered">
@foreach($schedules as $key=> $schedule)

    <tr>
        <td><strong>Day {{$key}}</strong></td>
        <td>
            @foreach($schedule as $key=> $sc)
                {{--                                                {{ dd($sc->speaker->speaker_type_id) }}--}}
                {{++$key. '. '.$sc->title}} {{ $sc->start_time }}
                <strong> ({{ $sc->users!=null?count($sc->users):0 }})</strong>
                @php
                    $paid= 0;

                @endphp
                @foreach($sc->users as $user)
                    @if($user->profile!=null)
                        @if($user->profile->payment_status=='1')
                            @php
                                $paid++;
                            @endphp
                        @endif
                    @endif
                @endforeach
                --- {{ 'Paid User'.': '.$paid}} ---
                {{  'Unpaid User'.": " .(count($sc->users)-$paid) }}
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
<div class="card">
<div class="card-header">
Relevant Resource
</div>
<div class="card-body">
<table class="table table-bordered">

@foreach( $blogs as $key=> $blog)

    <tr>
        <td>
            <a class="link-muted" href="{{ route('blogDetails',[$blog->id,$blog->slug]) }}" target="_blank">  <strong>
                    {{ $blog->title }} (<i class="fa fa-eye"></i> {{ $blog->views }})

                </strong></a>
        </td>
    </tr>
@endforeach


</table>

</div>
</div>
</div>
<div class="col-lg-12">
<div class="card">
<div class="card-header">
Last 10 days Report
</div>

<div class="card-body">
<div id="chartdiv2"></div>
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
<strong>Info!</strong> We have received your registration details; however, it appears that the payment for your account has not been completed yet. <strong>Please note that your seat is not confirmed until the payment is processed successfully
</strong>
</div>
@endif


<h2 style="color:red">
@if(auth()->user()->profile->payment_status=='1')
<strong>You paid: {{ auth()->user()->profile->pay_amount??"" }}, Thank you for your payment</strong>
@else
Your payable amount: {{ auth()->user()->profile->pay_amount??"" }}
@endif

{{  auth()->user()->profile->coupon_code!=null?" and You are used coupon code: " .auth()->user()->profile->coupon_code:""}}</h2>
<div class="row" style="margin: 50px 0px">
<div class="col-md-1">
   <strong> Payment Status:</strong>
</div>
<div class="col-md-11">
    @if(auth()->user()->profile->payment_status == '1')
        <button class="btn btn-success">Payment Complete</button>
    @else

        @if($settings['seat_is_full']=='false')
            <button class="btn btn-info" style="float: left;margin-right: 5px">Pending</button>

            @php
                $domain = explode('@', auth()->user()->profile->user->email);
            @endphp

            @if($settings['special_discount_is_true']=='true' and auth()->user()->profile->coupon_code==null and auth()->user()->profile->special_coupon=='REGSP300' and (in_array($domain[1], $allowedDomain)!=true) )
                <form action="{{ route('payNow') }}" method="post" style="width: 50px;float: left;">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->user()->profile->user_id }}">
                    <input type="hidden" name="special_discount" value="REGSP300">
                    <input  class="btn btn-danger" type="submit" value="Pay With Coupon extra {{ $settings['special_discount']??"0" }}% (REGSP300)">
                </form>
            @else
                <form action="{{ route('payNow') }}" method="post" style="width: 50px;float: left">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->user()->profile->user_id }}">
                    <input  class="btn btn-danger" type="submit" value="Pay Now">
                </form>

            @endif

        @else
            <button class="btn btn-info" style="float: left;margin-right: 5px">Seat is Full</button>
        @endcan
    @endif
</div>


</div>

</div>
<div class="col-lg-6">

<div class="card">
<div class="card-header">
Session Rating
</div>
<div class="card-body">




<form method="POST" action="{{ route('admin.feedback.store') }}">
    @csrf

    <div class="form-group">
        <label for="schedule">Session Rating :</label><br>
        <select name="schedule_id" id="schedule_id" required>
<option value=""> Select your session</option>
@foreach(auth()->user()->schedules()->get()->groupBy('day_number') as $dayNumber => $schedules)
{{--                                    <optgroup label="Day {{ $dayNumber }}">--}}

{{--                                        @foreach($schedules as $schedule)--}}
{{--                                            <option value="{{ $schedule->id }}">{{ $schedule->title }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </optgroup>--}}

                @foreach($schedules as $key=> $sc)

                    {{--                                                {{ dd($sc->speaker->speaker_type_id) }}--}}


                    @php
                        $currentDateTime = now();
                        $scheduledStartTime = \Carbon\Carbon::parse($settings['event_date'].$sc->start_time);
                                if ($sc->day_number==2){
                                $scheduledStartTime->addDays(1);
                                }
                        $scheduledDayNumber = $scheduledStartTime->day;
                    @endphp
                    @if($currentDateTime >= $scheduledStartTime)

                        @if($sc->attendance
 ->where('attendance_status',1)
 ->where('user_id',auth()->id())
 ->where('schedule_id',$sc->id)->first()!=null)

                            @if(\App\Models\Feedback::checkFeedback($sc->id, auth()->id())==true)
                                <option disabled value="{{ $sc->id }}">  {!! ++$key. '. '.$sc->title!!}
                                    -- Certificate Unlocked </option>
                            @else
                                <option value="{{ $sc->id }}">  {!! ++$key. '. '.$sc->title!!} </option>
                            @endif



                        @else
                        <option disabled value="{{ $sc->id }}">  {!! ++$key. '. '.$sc->title!!} -- Absent</option>
                        @endif
                    @else
                        <option disabled value="{{ $sc->id }}">  {!! ++$key. '. '.$sc->title!!}  -- Upcomming</option>
                    @endif
                @endforeach

@endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Rating:</label><br>
        <div class="rating">
            <input type="radio" id="star1" name="rating" value="1"><label for="star1"></label>
            <input type="radio" id="star2" name="rating" value="2"><label for="star2"></label>
            <input type="radio" id="star3" name="rating" value="3"><label for="star3"></label>
            <input type="radio" id="star4" name="rating" value="4"><label for="star4"></label>
            <input type="radio" id="star5" name="rating" value="5" checked><label for="star5"></label>
        </div>
    </div>

    <div class="form-group">
        <label for="comment">Comment:</label><br>
        <textarea name="comment" id="comment" rows="4" cols="50"></textarea>
    </div>


    <button class="btn btn-info" type="submit">Rating</button>
</form>






</div>
</div>



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
<div class="card">
<div class="card-header">
My Registered Workshop List
</div>
<div class="card-body">
<table class="table table-bordered">

    @foreach( auth()->user()->schedules()->get()->groupBy('day_number') as $key=> $schedule)

        <tr>
            <td><strong>Day {{$key}}</strong></td>
            <td>
                @foreach($schedule as $key=> $sc)
                    {{--                                                {{ dd($sc->speaker->speaker_type_id) }}--}}
                    {!! ++$key. '. '.$sc->title!!}
                    <br>
                    <strong>  Start Time & Duration: {{ \Carbon\Carbon::parse($sc->start_time)->format('h:i A') }} -- {{ $sc->subtitle }}</strong>
                    {{--                                                                 new condition here--}}
                    @php
                        $currentDateTime = now();

                        $scheduledStartTime = \Carbon\Carbon::parse($settings['event_date'].$sc->start_time);
                                if ($sc->day_number==2){
                                $scheduledStartTime->addDays(1);
                                }
                        $scheduledDayNumber = $scheduledStartTime->day;
//nes script add when developed rating and previous script review must n


                    @endphp

                    @if( $currentDateTime >= $scheduledStartTime)
                        @if($sc->attendance
->where('attendance_status',1)
->where('user_id',auth()->id())
->where('schedule_id',$sc->id)->first()!=null)
                            @if(\App\Models\Feedback::checkFeedback($sc->id, auth()->id())==true)
                            <a href="{{ route('admin.downloadCertificate',$sc->id) }}" style="color: #8D12D1; cursor: pointer">
                                Download Certificate
                            </a>
                            @else
                               <span style="color:red"> {{ "Feedback First: Unlock Your Certificate" }}</span>
                            @endif

                        @else
                            <strong style="color: darkred">Absent</strong>
                        @endif
                    @else
                        <strong style="color: darkred">Upcoming</strong>
                    @endif

                    <br>

                @endforeach
            </td>
        </tr>
    @endforeach


</table>

</div>
</div>
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
#chartdiv2 {
width: 100%;
height: 500px;
}
#chartdiv3 {
width: 100%;
height: 500px;
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
<script>
var registrations = {!! json_encode($registrations) !!};
console.log(registrations);
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end



var chart = am4core.create('chartdiv2', am4charts.XYChart)
chart.colors.step = 2;

chart.legend = new am4charts.Legend()
chart.legend.position = 'top'
chart.legend.paddingBottom = 20
chart.legend.labels.template.maxWidth = 95

var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
xAxis.dataFields.category = 'date'
xAxis.renderer.cellStartLocation = 0.1
xAxis.renderer.cellEndLocation = 0.9
xAxis.renderer.grid.template.location = 0;

var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
yAxis.min = 0;

function createSeries(value, name) {
var series = chart.series.push(new am4charts.ColumnSeries())
series.dataFields.valueY = value
series.dataFields.categoryX = 'date'
series.name = name

series.events.on("hidden", arrangeColumns);
series.events.on("shown", arrangeColumns);

var bullet = series.bullets.push(new am4charts.LabelBullet())
bullet.interactionsEnabled = false
bullet.dy = 30;
bullet.label.text = '{valueY}'
bullet.label.fill = am4core.color('#ffffff')

return series;
}

chart.data = registrations;


createSeries('total_users', 'Registration');
createSeries('paid', 'Paid');
createSeries('unpaid', 'Unpaid');

function arrangeColumns() {

var series = chart.series.getIndex(0);

var w = 1 - xAxis.renderer.cellStartLocation - (1 - xAxis.renderer.cellEndLocation);
if (series.dataItems.length > 1) {
var x0 = xAxis.getX(series.dataItems.getIndex(0), "categoryX");
var x1 = xAxis.getX(series.dataItems.getIndex(1), "categoryX");
var delta = ((x1 - x0) / chart.series.length) * w;
if (am4core.isNumber(delta)) {
var middle = chart.series.length / 2;

var newIndex = 0;
chart.series.each(function(series) {
if (!series.isHidden && !series.isHiding) {
series.dummyData = newIndex;
newIndex++;
}
else {
series.dummyData = chart.series.indexOf(series);
}
})
var visibleCount = newIndex;
var newMiddle = visibleCount / 2;

chart.series.each(function(series) {
var trueIndex = chart.series.indexOf(series);
var newIndex = series.dummyData;

var dx = (newIndex - trueIndex + middle - newMiddle) * delta

series.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
series.bulletsContainer.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
})
}
}
}

}); // end am4core.ready()
</script>
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
var totalPayAmount = {!! json_encode($totalPayAmount) !!};
var totalTaka = {!! json_encode($totalTaka) !!};
am4core.ready(function() {
// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end
var chart = am4core.create("chartdiv3", am4charts.PieChart3D);
chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
chart.data = totalTaka;
if (totalPayAmount === 0) {
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
label.text = "Total\n{{ $totalPayAmount }}"; // use \n instead of <br>
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
