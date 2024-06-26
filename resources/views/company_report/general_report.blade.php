@extends('layouts.master')
@section('title')
    {{trans_choice('general.portfolio_overview',1)}}
@endsection
@section('content')
    <div style="display: none;" class="panel panel-white">
        <div style="display: none;" class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.report',1)}} {{trans_choice('general.general',2)}}
                @if(!empty($end_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body hidden-print">
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div style="display: none;" class="row">
                <div class="col-md-4">
                    {!! Form::label('start_date',trans_choice('general.start',1).' '.trans_choice('general.date',1),array('class'=>'')) !!}
                    {!! Form::text('start_date',$start_date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('end_date',trans_choice('general.end',1).' '.trans_choice('general.date',1),array('class'=>'')) !!}
                    {!! Form::text('end_date',$end_date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">

                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!
                        </button>


                        <a href="{{Request::url()}}"
                           class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>

                        <div class="btn-group hidden">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/company_report/products_summary/pdf?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/company_report/products_summary/excel?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/company_report/products_summary/csv?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->


    <div class="row">

        <div class="col-md-6">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.distribution_by_type_of_loan',1)}}<!--{{trans_choice('general.loan',1)}} {{trans_choice('general.product',2)}} ---></h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body  no-padding">
                    <div class="panel-body">
                        <p>
                            In the pie chart below, we illustrate the current loan distribution as of <?php echo date('F Y'); ?>. The breakdown is as follows:
                        </p>
                    </div>

                    <div id="loan_product_data" class="chart" style="height: 724px;">

                    </div>
                    <div class="panel-body">
                        <p class="text-justify">{{ $description4 ?? 'No description available' }}</p>
                    </div>

                </div>
            </div>

        </div>

{{--        --}}{{--<div class="col-md-6">--}}
{{--            <div class="panel panel-white">--}}
{{--                <div class="panel-heading">--}}
{{--                    <h6 class="panel-title">{{trans_choice('general.summary_income_vs_expenses',1)}}<!---{{trans_choice('general.income',1)}} {{trans_choice('general.vs',2)}} {{trans_choice('general.expense',2)}}---></h6>--}}

{{--                    <div class="heading-elements">--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="panel-body  no-padding">--}}
{{--                    <div id="monthly_net_income_data" class="chart" style="height: 320px;">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.customer_registration_summary_by_month',1)}}<!---{{trans_choice('general.borrower',1)}} {{trans_choice('general.registration',2)}} ---></h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body  no-padding">
                    <div id="monthly_borrower_data" class="chart" style="height: 320px;">

                    </div>
                    <div class="panel-body">
                        <p class="text-justify">{{ $description ?? 'No description available' }}</p>


                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-6">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.summary_payment_received_vs_expected_payment',1)}}</h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body  no-padding">
                    <div id="monthly_actual_expected_data" class="chart" style="height: 320px;">
                    </div>
                    <div class="panel-body">
                        <p class="text-justify">{{ $description1 ?? 'No description available' }}</p>
{{--                        <p>--}}
{{--                            The graph below illustrates a comparison between Payments Received and Expected Payments over time:--}}
{{--                        </p>--}}

{{--                        <!-- Insert your graph here, using the description provided -->--}}

{{--                        <p>--}}
{{--                            The horizontal axis represents time intervals (e.g., months, quarters, or years), while the vertical axis depicts the monetary values. Two lines are plotted on the graph:--}}
{{--                        </p>--}}

{{--                        <ul>--}}
{{--                            <li><strong>Payments Received: {{ $description1 }}</strong><br>--}}

{{--                            </li>--}}
{{--                            <li><strong>Expected Payments: {{ $description2 }}</strong><br>--}}

{{--                            </li>--}}
{{--                        </ul>--}}


                    </div>

                </div>
            </div>

        </div>
    </div>
    <div style="display: none;" class="row">
        <div class="col-md-12">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.capital_disbursed_per_month',1)}}<!--  {{trans_choice('general.disbursed',1)}} {{trans_choice('general.principal',1)}}--> </h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body  no-padding">
                    <div id="monthly_disbursed_loans_data" class="chart" style="height: 420px;">
                    </div>

                </div>
            </div>

        </div>
    </div>


{{--        <div class="panel-body">--}}
{{--            <p>--}}
{{--                In the pie chart below, we illustrate the current loan distribution as of {{ date('F Y') }}. The breakdown is as follows:--}}
{{--            </p>--}}
{{--            <ul>--}}
{{--                <li>Educational Loan: {{ $loan_distribution['educational'] }}%</li>--}}
{{--                <li>Emergency Loan: {{ $loan_distribution['emergency'] }}%</li>--}}
{{--                <li>PEI Loan: {{ $loan_distribution['pei'] }}%</li>--}}
{{--                <li>Bonus Loan: {{ $loan_distribution['bonus'] }}%</li>--}}
{{--                <li>Regular Loan: {{ $loan_distribution['regular'] }}%</li>--}}
{{--                <li>Gadget Loan: {{ $loan_distribution['gadget'] }}%</li>--}}
{{--            </ul>--}}
{{--            <p>--}}
{{--                This breakdown emphasizes the predominant influence of emergency loans and the specific allocation of educational--}}
{{--                loans within the broader financial landscape.--}}
{{--            </p>--}}
{{--        </div>--}}

    <script>
        $(document).ready(function () {
            $("body").addClass('sidebar-xs');
        });
    </script>
    <script src="{{ asset('assets/plugins/amcharts/amcharts.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/serial.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/pie.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/themes/light.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/plugins/export/export.min.js') }}"
            type="text/javascript"></script>
    <script>

        var chart = AmCharts.makeChart("loan_product_data", {
            "type": "pie",
            "theme": "light",
            "dataProvider":{!! $loan_product_data !!},
            "valueField": "value",
            "titleField": "product",
            "balloon": {
                "fixedPosition": true
            },
            "export": {
                "enabled": true,
                "libs": {
                    "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                }
            }, "legend": {
                "position": "bottom",
                "marginRight": 100,
                "autoMargins": false
            },
        });
        AmCharts.makeChart("monthly_net_income_data", {
            "type": "serial",
            "theme": "light",
            "autoMargins": true,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',
            "color": '#888',

            "dataProvider": {!! $monthly_net_income_data !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#370fc6",
                "lineThickness": 4,
                "negativeLineColor": "#0dd102",
                "title": "{{trans_choice('general.income',1)}}",
                "type": "smoothedLine",
                "valueField": "income"
            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#d1655d",
                "lineThickness": 4,
                "negativeLineColor": "#d1cf0d",
                "title": "{{trans_choice('general.expense',2)}}",
                "type": "smoothedLine",
                "valueField": "expenses"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }, "export": {
                "enabled": true,
                "libs": {
                    "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                }
            }, "legend": {
                "position": "bottom",
                "marginRight": 100,
                "autoMargins": false
            },


        });
        AmCharts.makeChart("monthly_borrower_data", {
            "type": "serial",
            "theme": "light",
            "autoMargins": true,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',
            "color": '#888',

            "dataProvider": {!! $monthly_borrower_data !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#1bd126",
                "lineThickness": 4,
                "negativeLineColor": "#637bb6",
                "title": "{{trans_choice('general.borrower',2)}}",
                "type": "smoothedLine",
                "valueField": "value"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }, "export": {
                "enabled": true,
                "libs": {
                    "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                }
            }, "legend": {
            "position": "bottom",
                "marginRight": 100,
                "autoMargins": false
        },

        });
        AmCharts.makeChart("monthly_actual_expected_data", {
            "type": "serial",
            "theme": "light",
            "autoMargins": true,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',
            "color": '#888',

            "dataProvider": {!! $monthly_actual_expected_data !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "lineColor": "#370fc6",
                "fillAlphas": 1,
                "negativeLineColor": "#0dd102",
                "title": "Received",
                "type": "column",
                "valueField": "actual"
            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",

                "lineColor": "#d1655d",
                "fillAlphas": 1,
                "negativeLineColor": "#d1cf0d",
                "title": "Expected",
                "type": "column",
                "valueField": "expected"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }, "export": {
                "enabled": true,
                "libs": {
                    "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                }
            }, "legend": {
                "position": "bottom",
                "marginRight": 100,
                "autoMargins": false
            },


        });
        AmCharts.makeChart("monthly_disbursed_loans_data", {
            "type": "serial",
            "theme": "light",
            "autoMargins": true,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',
            "color": '#888',

            "dataProvider": {!! $monthly_disbursed_loans_data !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "lineColor": "#370fc6",
                "fillAlphas": 1,
                "negativeLineColor": "#0dd102",
                "title": "{{trans_choice('general.principal',1)}}",
                "type": "column",
                "valueField": "value"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }, "export": {
                "enabled": true,
                "libs": {
                    "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                }
            }


        });
    </script>
@endsection
@section('footer-scripts')

@endsection
