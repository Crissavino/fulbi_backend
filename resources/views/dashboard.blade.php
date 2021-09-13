@extends('layouts.app', ['activePage' => 'dashboard', 'titlePage' => __('Fulbito Dashboard')])

@section('content')
    <style>

        .card .map {
            height: 500px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .map {
            top: 0;
            bottom: 0;
            height: 100%;
            width: 100%;
            overflow: visible;
            box-shadow: 0 4px 14px -4px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }
    </style>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-success card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">sports_soccer</i>
                            </div>
                            <p class="card-category">Matches</p>
                            <h3 class="card-title">{{ $matches->count()  }}</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons">date_range</i> New this week: {{ $matchesThisWeek }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-warning card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">people</i>
                            </div>
                            <p class="card-category">Users</p>
                            <h3 class="card-title">{{ $users->count()  }}</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                New this week: {{ $newUsersThisWeek }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-danger card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">apple</i>
                            </div>
                            <p class="card-category">Apple Users</p>
                            <h3 class="card-title">{{ $iosDevices->count() }}</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons">update</i> New this week: {{ $iosThisWeek }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-header card-header-info card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">android</i>
                            </div>
                            <p class="card-category">Android Users</p>
                            <h3 class="card-title">{{ $androidDevices->count() }}</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons">update</i> New this week: {{ $androidThisWeek }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-chart">
                        <div class="card-header card-header-success">
                            <div class="ct-chart" id="dailySalesChart"></div>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">Created Matches</h4>
                            <p class="card-category">
                                <span class="text-success"><i class="fa fa-long-arrow-up"></i> 55% </span> increase in
                                today sales.</p>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons">access_time</i> updated 4 minutes ago
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-chart">
                        <div class="card-header card-header-danger">
                            <div class="ct-chart" id="completedTasksChart"></div>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">Apple Users</h4>
                            <p class="card-category">Last Campaign Performance</p>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons">access_time</i> campaign sent 2 days ago
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-chart">
                        <div class="card-header card-header-warning">
                            <div class="ct-chart" id="websiteViewsChart"></div>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">Android Users</h4>
                            <p class="card-category">Last Campaign Performance</p>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons">access_time</i> campaign sent 2 days ago
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-chart">
                        <div class="card-header card-header-success">
                            <h3 class="text-center font-weight-bold">User by country</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class=" text-primary">
                                    <th>Country</th>
                                    <th>Players</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($byCountry as $country => $locations)
                                        <tr>
                                            <td>
                                                @if(empty($country))
                                                    Unknown
                                                @else
                                                    {{$country}}
                                                @endif
                                            </td>
                                            <td>{{$locations->count()}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-chart">
                        <div class="card-header card-header-danger">
                            <h3 class="text-center font-weight-bold">User by province</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class=" text-primary">
                                    <th>Province</th>
                                    <th>Players</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($byProvince as $province => $locations)
                                        <tr>
                                            <td>
                                                @if(empty($province))
                                                Unknown
                                                @else
                                                {{$province}}
                                                @endif
                                            </td>
                                            <td>{{$locations->count()}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-chart">
                        <div class="card-header card-header-warning">
                            <h3 class="text-center font-weight-bold">User by city</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class=" text-primary">
                                    <th>City</th>
                                    <th>Players</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($byCity as $city => $locations)
                                        <tr>
                                            <td>
                                                @if(empty($city))
                                                    Unknown
                                                @else
                                                    {{$city}}
                                                @endif </td>
                                            <td>{{$locations->count()}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            // Javascript method's body can be found in assets/js/demos.js
            if ($('#dailySalesChart').length != 0 || $('#completedTasksChart').length != 0 || $('#websiteViewsChart').length != 0) {

                /* ----------==========     Daily Sales Chart initialization    ==========---------- */

                let matchesChartData = {!! json_encode($matchesChartData) !!};
                dataDailySalesChart = {
                    labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
                    series: [
                        [matchesChartData['monday'], matchesChartData['tuesday'], matchesChartData['wednesday'], matchesChartData['thursday'], matchesChartData['friday'] , matchesChartData['saturday'], matchesChartData['sunday']]
                    ]
                };

                optionsDailySalesChart = {
                    lineSmooth: Chartist.Interpolation.cardinal({
                        tension: 0
                    }),
                    low: 0,
                    high: matchesChartData['highNumberForChart'], // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                    chartPadding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    },
                }

                let dailySalesChart = new Chartist.Line('#dailySalesChart', dataDailySalesChart, optionsDailySalesChart);

                md.startAnimationForLineChart(dailySalesChart);

                /* ----------==========     Completed Tasks Chart initialization    ==========---------- */

                let iosChartData = {!! json_encode($iosChartData) !!};
                dataCompletedTasksChart = {
                    labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
                    series: [
                        [iosChartData['monday'], iosChartData['tuesday'], iosChartData['wednesday'], iosChartData['thursday'], iosChartData['friday'] , iosChartData['saturday'], iosChartData['sunday']]
                    ]
                };

                optionsCompletedTasksChart = {
                    lineSmooth: Chartist.Interpolation.cardinal({
                        tension: 0
                    }),
                    low: 0,
                    high: iosChartData['highNumberForChart'], // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                    chartPadding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    }
                }

                let completedTasksChart = new Chartist.Line('#completedTasksChart', dataCompletedTasksChart, optionsCompletedTasksChart);

                // start animation for the Completed Tasks Chart - Line Chart
                md.startAnimationForLineChart(completedTasksChart);

                /* ----------==========     Emails Subscription Chart initialization    ==========---------- */

                let androidChartData = {!! json_encode($androidChartData) !!};
                let dataWebsiteViewsChart = {
                    labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
                    series: [
                        [androidChartData['monday'], androidChartData['tuesday'], androidChartData['wednesday'], androidChartData['thursday'], androidChartData['friday'] , androidChartData['saturday'], androidChartData['sunday']]
                    ]
                };
                let optionsWebsiteViewsChart = {
                    axisX: {
                        showGrid: false
                    },
                    low: 0,
                    high: androidChartData['highNumberForChart'],
                    chartPadding: {
                        top: 0,
                        right: 5,
                        bottom: 0,
                        left: 0
                    }
                };
                let responsiveOptions = [
                    ['screen and (max-width: 640px)', {
                        seriesBarDistance: 5,
                        axisX: {
                            labelInterpolationFnc: function (value) {
                                return value[0];
                            }
                        }
                    }]
                ];
                // let websiteViewsChart = Chartist.Bar('#websiteViewsChart', dataWebsiteViewsChart, optionsWebsiteViewsChart, responsiveOptions);
                let websiteViewsChart = Chartist.Line('#websiteViewsChart', dataWebsiteViewsChart, optionsWebsiteViewsChart, responsiveOptions);

                //start animation for the Emails Subscription Chart
                md.startAnimationForBarChart(websiteViewsChart);
            }
        });
    </script>
@endpush
