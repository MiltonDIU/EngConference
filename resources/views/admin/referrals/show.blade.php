@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.referral.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.referrals.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.referral.fields.id') }}
                        </th>
                        <td>
                            {{ $referral->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.referral.fields.name') }}
                        </th>
                        <td>
                            {{ $referral->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.referral.fields.email') }}
                        </th>
                        <td>
                            {{ $referral->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.referral.fields.identification') }}
                        </th>
                        <td>
                            {{ $referral->identification }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Referral Link
                        </th>
                        <td>
                           {{ route('book-ticket-referral',$referral->identification) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.referral.fields.is_active') }}
                        </th>
                        <td>
                            {{ App\Models\Referral::IS_ACTIVE_SELECT[$referral->is_active] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.referral.fields.avatar') }}
                        </th>
                        <td>
                            @if($referral->avatar)
                                <a href="{{ $referral->avatar->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $referral->avatar->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.referrals.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    Impression, Referral, Conversion
                </div>

                <div class="card-body">
                    <div id="chartdiv"></div>
                </div>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="tab-content active">
            <div class="tab-pane active" role="tabpanel">
                @includeIf('admin.referrals.relationships.referral_visitors', ['referrals' => $referral->referralVisitor])
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
        .link-muted:hover{color: #7016B6}
    </style>
@endpush

@push('script')
    <!-- Styles -->
    <style>
        #chartdiv {
            width: 100%;
            height: 500px;
        }
    </style>

    <!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

    <!-- Chart code -->
    <script>
        var profiles = {!! json_encode($profiles) !!};
        am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
            var root = am5.Root.new("chartdiv");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([
                am5themes_Animated.new(root)
            ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/xy-chart/
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX: true
            }));

// Add cursor
// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
            cursor.lineY.set("visible", false);


// Create axes
// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
            var xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
            xRenderer.labels.template.setAll({
                rotation: -90,
                centerY: am5.p50,
                centerX: am5.p100,
                paddingRight: 15
            });

            xRenderer.grid.template.setAll({
                location: 1
            })

            var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                maxDeviation: 0.3,
                categoryField: "country",
                renderer: xRenderer,
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                maxDeviation: 0.3,
                renderer: am5xy.AxisRendererY.new(root, {
                    strokeOpacity: 0.1
                })
            }));


// Create series
// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
            var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: "Series 1",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                sequencedInterpolation: true,
                categoryXField: "country",
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{valueY}"
                })
            }));

            series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0 });
            series.columns.template.adapters.add("fill", function(fill, target) {
                return chart.get("colors").getIndex(series.columns.indexOf(target));
            });

            series.columns.template.adapters.add("stroke", function(stroke, target) {
                return chart.get("colors").getIndex(series.columns.indexOf(target));
            });


// Set data
            var data = profiles;
//             var data = [{
//                 country: "I",
//                 value: 100
//             }, {
//                 country: "China",
//                 value: 56
//             }, {
//                 country: "Japan",
//                 value: 15
//             }, {
//                 country: "Germany",
//                 value: 41
//             }];
// console.log(data);
// console.log(profiles);
            xAxis.data.setAll(data);
            series.data.setAll(data);


// Make stuff animate on load
// https://www.amcharts.com/docs/v5/concepts/animations/
            series.appear(1000);
            chart.appear(1000, 100);

        }); // end am5.ready()
    </script>



{{--    <!-- Resources -->--}}
{{--    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>--}}
{{--    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>--}}
{{--    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>--}}

{{--    <!-- Chart code -->--}}
{{--    <script>--}}
{{--        var total = {!! json_encode($total) !!};--}}
{{--        var profiles = {!! json_encode($profiles) !!};--}}
{{--        am4core.ready(function() {--}}
{{--// Themes begin--}}
{{--            am4core.useTheme(am4themes_animated);--}}
{{--// Themes end--}}
{{--            var chart = am4core.create("chartdiv", am4charts.PieChart3D);--}}
{{--            chart.hiddenState.properties.opacity = 0; // this creates initial fade-in--}}
{{--            // chart.data = [--}}
{{--            //     {--}}
{{--            //         country: "Paid",--}}
{{--            //         litres: 200.9--}}
{{--            //     },--}}
{{--            //     {--}}
{{--            //         country: "Unpaid",--}}
{{--            //         litres: 60--}}
{{--            //     }--}}
{{--            // ];--}}
{{--           chart.data = profiles;--}}
{{--            if (total === 0) {--}}
{{--                // Create a new chart with a default value of "No data available"--}}
{{--                var chart = am4core.create("totalOverview", am4charts.PieChart3D);--}}
{{--                chart.innerRadius = 100;--}}
{{--                var label = chart.seriesContainer.createChild(am4core.Label);--}}
{{--                label.text = "No data available";--}}
{{--                label.horizontalCenter = "middle";--}}
{{--                label.verticalCenter = "middle";--}}
{{--                label.fontSize = 30;--}}
{{--                label.y = -20;--}}
{{--                label.multiline = true;--}}
{{--                label.textAlign = "center";--}}
{{--            }else{--}}
{{--                chart.innerRadius = 100;--}}
{{--                var label = chart.seriesContainer.createChild(am4core.Label);--}}
{{--                label.text = "Impression\n{{ $total }}"; // use \n instead of <br>--}}
{{--                label.horizontalCenter = "middle";--}}
{{--                label.verticalCenter = "middle";--}}
{{--                label.fontSize = 30;--}}
{{--                label.y = -25; // move label 20 pixels upwards from the center--}}
{{--                label.multiline = true; // enable multiline text--}}
{{--                label.textAlign = "center"; // center-align the text within the label--}}
{{--            }--}}


{{--            chart.innerRadius = am4core.percent(40);--}}
{{--            chart.depth = 15;--}}
{{--            chart.legend = new am4charts.Legend();--}}

{{--            var series = chart.series.push(new am4charts.PieSeries3D());--}}
{{--            series.dataFields.value = "litres";--}}
{{--            series.dataFields.depthValue = "litres";--}}
{{--            series.dataFields.category = "country";--}}
{{--            series.slices.template.cornerRadius = 3;--}}
{{--            series.colors.step = 3;--}}
{{--        }); // end am4core.ready()--}}
{{--    </script>--}}


@endpush
