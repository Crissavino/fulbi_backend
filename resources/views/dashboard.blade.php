@extends('layouts.app', ['activePage' => 'dashboard', 'titlePage' => __('Fulbito Dashboard')])

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
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
              <h4 class="card-title">Daily Sales</h4>
              <p class="card-category">
                <span class="text-success"><i class="fa fa-long-arrow-up"></i> 55% </span> increase in today sales.</p>
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
            <div class="card-header card-header-warning">
              <div class="ct-chart" id="websiteViewsChart"></div>
            </div>
            <div class="card-body">
              <h4 class="card-title">Email Subscriptions</h4>
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
            <div class="card-header card-header-danger">
              <div class="ct-chart" id="completedTasksChart"></div>
            </div>
            <div class="card-body">
              <h4 class="card-title">Completed Tasks</h4>
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
{{--      <div class="row">--}}
{{--        <div class="col-lg-6 col-md-12">--}}
{{--          <div class="card">--}}
{{--            <div class="card-header card-header-tabs card-header-primary">--}}
{{--              <div class="nav-tabs-navigation">--}}
{{--                <div class="nav-tabs-wrapper">--}}
{{--                  <span class="nav-tabs-title">Tasks:</span>--}}
{{--                  <ul class="nav nav-tabs" data-tabs="tabs">--}}
{{--                    <li class="nav-item">--}}
{{--                      <a class="nav-link active" href="#profile" data-toggle="tab">--}}
{{--                        <i class="material-icons">bug_report</i> Bugs--}}
{{--                        <div class="ripple-container"></div>--}}
{{--                      </a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                      <a class="nav-link" href="#messages" data-toggle="tab">--}}
{{--                        <i class="material-icons">code</i> Website--}}
{{--                        <div class="ripple-container"></div>--}}
{{--                      </a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                      <a class="nav-link" href="#settings" data-toggle="tab">--}}
{{--                        <i class="material-icons">cloud</i> Server--}}
{{--                        <div class="ripple-container"></div>--}}
{{--                      </a>--}}
{{--                    </li>--}}
{{--                  </ul>--}}
{{--                </div>--}}
{{--              </div>--}}
{{--            </div>--}}
{{--            <div class="card-body">--}}
{{--              <div class="tab-content">--}}
{{--                <div class="tab-pane active" id="profile">--}}
{{--                  <table class="table">--}}
{{--                    <tbody>--}}
{{--                      <tr>--}}
{{--                        <td>--}}
{{--                          <div class="form-check">--}}
{{--                            <label class="form-check-label">--}}
{{--                              <input class="form-check-input" type="checkbox" value="" checked>--}}
{{--                              <span class="form-check-sign">--}}
{{--                                <span class="check"></span>--}}
{{--                              </span>--}}
{{--                            </label>--}}
{{--                          </div>--}}
{{--                        </td>--}}
{{--                        <td>Sign contract for "What are conference organizers afraid of?"</td>--}}
{{--                        <td class="td-actions text-right">--}}
{{--                          <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">--}}
{{--                            <i class="material-icons">edit</i>--}}
{{--                          </button>--}}
{{--                          <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">--}}
{{--                            <i class="material-icons">close</i>--}}
{{--                          </button>--}}
{{--                        </td>--}}
{{--                      </tr>--}}
{{--                      <tr>--}}
{{--                        <td>--}}
{{--                          <div class="form-check">--}}
{{--                            <label class="form-check-label">--}}
{{--                              <input class="form-check-input" type="checkbox" value="">--}}
{{--                              <span class="form-check-sign">--}}
{{--                                <span class="check"></span>--}}
{{--                              </span>--}}
{{--                            </label>--}}
{{--                          </div>--}}
{{--                        </td>--}}
{{--                        <td>Lines From Great Russian Literature? Or E-mails From My Boss?</td>--}}
{{--                        <td class="td-actions text-right">--}}
{{--                          <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">--}}
{{--                            <i class="material-icons">edit</i>--}}
{{--                          </button>--}}
{{--                          <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">--}}
{{--                            <i class="material-icons">close</i>--}}
{{--                          </button>--}}
{{--                        </td>--}}
{{--                      </tr>--}}
{{--                      <tr>--}}
{{--                        <td>--}}
{{--                          <div class="form-check">--}}
{{--                            <label class="form-check-label">--}}
{{--                              <input class="form-check-input" type="checkbox" value="">--}}
{{--                              <span class="form-check-sign">--}}
{{--                                <span class="check"></span>--}}
{{--                              </span>--}}
{{--                            </label>--}}
{{--                          </div>--}}
{{--                        </td>--}}
{{--                        <td>Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit--}}
{{--                        </td>--}}
{{--                        <td class="td-actions text-right">--}}
{{--                          <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">--}}
{{--                            <i class="material-icons">edit</i>--}}
{{--                          </button>--}}
{{--                          <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">--}}
{{--                            <i class="material-icons">close</i>--}}
{{--                          </button>--}}
{{--                        </td>--}}
{{--                      </tr>--}}
{{--                      <tr>--}}
{{--                        <td>--}}
{{--                          <div class="form-check">--}}
{{--                            <label class="form-check-label">--}}
{{--                              <input class="form-check-input" type="checkbox" value="" checked>--}}
{{--                              <span class="form-check-sign">--}}
{{--                                <span class="check"></span>--}}
{{--                              </span>--}}
{{--                            </label>--}}
{{--                          </div>--}}
{{--                        </td>--}}
{{--                        <td>Create 4 Invisible User Experiences you Never Knew About</td>--}}
{{--                        <td class="td-actions text-right">--}}
{{--                          <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">--}}
{{--                            <i class="material-icons">edit</i>--}}
{{--                          </button>--}}
{{--                          <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">--}}
{{--                            <i class="material-icons">close</i>--}}
{{--                          </button>--}}
{{--                        </td>--}}
{{--                      </tr>--}}
{{--                    </tbody>--}}
{{--                  </table>--}}
{{--                </div>--}}
{{--                <div class="tab-pane" id="messages">--}}
{{--                  <table class="table">--}}
{{--                    <tbody>--}}
{{--                      <tr>--}}
{{--                        <td>--}}
{{--                          <div class="form-check">--}}
{{--                            <label class="form-check-label">--}}
{{--                              <input class="form-check-input" type="checkbox" value="" checked>--}}
{{--                              <span class="form-check-sign">--}}
{{--                                <span class="check"></span>--}}
{{--                              </span>--}}
{{--                            </label>--}}
{{--                          </div>--}}
{{--                        </td>--}}
{{--                        <td>Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit--}}
{{--                        </td>--}}
{{--                        <td class="td-actions text-right">--}}
{{--                          <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">--}}
{{--                            <i class="material-icons">edit</i>--}}
{{--                          </button>--}}
{{--                          <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">--}}
{{--                            <i class="material-icons">close</i>--}}
{{--                          </button>--}}
{{--                        </td>--}}
{{--                      </tr>--}}
{{--                      <tr>--}}
{{--                        <td>--}}
{{--                          <div class="form-check">--}}
{{--                            <label class="form-check-label">--}}
{{--                              <input class="form-check-input" type="checkbox" value="">--}}
{{--                              <span class="form-check-sign">--}}
{{--                                <span class="check"></span>--}}
{{--                              </span>--}}
{{--                            </label>--}}
{{--                          </div>--}}
{{--                        </td>--}}
{{--                        <td>Sign contract for "What are conference organizers afraid of?"</td>--}}
{{--                        <td class="td-actions text-right">--}}
{{--                          <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">--}}
{{--                            <i class="material-icons">edit</i>--}}
{{--                          </button>--}}
{{--                          <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">--}}
{{--                            <i class="material-icons">close</i>--}}
{{--                          </button>--}}
{{--                        </td>--}}
{{--                      </tr>--}}
{{--                    </tbody>--}}
{{--                  </table>--}}
{{--                </div>--}}
{{--                <div class="tab-pane" id="settings">--}}
{{--                  <table class="table">--}}
{{--                    <tbody>--}}
{{--                      <tr>--}}
{{--                        <td>--}}
{{--                          <div class="form-check">--}}
{{--                            <label class="form-check-label">--}}
{{--                              <input class="form-check-input" type="checkbox" value="">--}}
{{--                              <span class="form-check-sign">--}}
{{--                                <span class="check"></span>--}}
{{--                              </span>--}}
{{--                            </label>--}}
{{--                          </div>--}}
{{--                        </td>--}}
{{--                        <td>Lines From Great Russian Literature? Or E-mails From My Boss?</td>--}}
{{--                        <td class="td-actions text-right">--}}
{{--                          <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">--}}
{{--                            <i class="material-icons">edit</i>--}}
{{--                          </button>--}}
{{--                          <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">--}}
{{--                            <i class="material-icons">close</i>--}}
{{--                          </button>--}}
{{--                        </td>--}}
{{--                      </tr>--}}
{{--                      <tr>--}}
{{--                        <td>--}}
{{--                          <div class="form-check">--}}
{{--                            <label class="form-check-label">--}}
{{--                              <input class="form-check-input" type="checkbox" value="" checked>--}}
{{--                              <span class="form-check-sign">--}}
{{--                                <span class="check"></span>--}}
{{--                              </span>--}}
{{--                            </label>--}}
{{--                          </div>--}}
{{--                        </td>--}}
{{--                        <td>Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit--}}
{{--                        </td>--}}
{{--                        <td class="td-actions text-right">--}}
{{--                          <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">--}}
{{--                            <i class="material-icons">edit</i>--}}
{{--                          </button>--}}
{{--                          <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">--}}
{{--                            <i class="material-icons">close</i>--}}
{{--                          </button>--}}
{{--                        </td>--}}
{{--                      </tr>--}}
{{--                      <tr>--}}
{{--                        <td>--}}
{{--                          <div class="form-check">--}}
{{--                            <label class="form-check-label">--}}
{{--                              <input class="form-check-input" type="checkbox" value="" checked>--}}
{{--                              <span class="form-check-sign">--}}
{{--                                <span class="check"></span>--}}
{{--                              </span>--}}
{{--                            </label>--}}
{{--                          </div>--}}
{{--                        </td>--}}
{{--                        <td>Sign contract for "What are conference organizers afraid of?"</td>--}}
{{--                        <td class="td-actions text-right">--}}
{{--                          <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">--}}
{{--                            <i class="material-icons">edit</i>--}}
{{--                          </button>--}}
{{--                          <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">--}}
{{--                            <i class="material-icons">close</i>--}}
{{--                          </button>--}}
{{--                        </td>--}}
{{--                      </tr>--}}
{{--                    </tbody>--}}
{{--                  </table>--}}
{{--                </div>--}}
{{--              </div>--}}
{{--            </div>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--        <div class="col-lg-6 col-md-12">--}}
{{--          <div class="card">--}}
{{--            <div class="card-header card-header-warning">--}}
{{--              <h4 class="card-title">Employees Stats</h4>--}}
{{--              <p class="card-category">New employees on 15th September, 2016</p>--}}
{{--            </div>--}}
{{--            <div class="card-body table-responsive">--}}
{{--              <table class="table table-hover">--}}
{{--                <thead class="text-warning">--}}
{{--                  <th>ID</th>--}}
{{--                  <th>Name</th>--}}
{{--                  <th>Salary</th>--}}
{{--                  <th>Country</th>--}}
{{--                </thead>--}}
{{--                <tbody>--}}
{{--                  <tr>--}}
{{--                    <td>1</td>--}}
{{--                    <td>Dakota Rice</td>--}}
{{--                    <td>$36,738</td>--}}
{{--                    <td>Niger</td>--}}
{{--                  </tr>--}}
{{--                  <tr>--}}
{{--                    <td>2</td>--}}
{{--                    <td>Minerva Hooper</td>--}}
{{--                    <td>$23,789</td>--}}
{{--                    <td>Curaçao</td>--}}
{{--                  </tr>--}}
{{--                  <tr>--}}
{{--                    <td>3</td>--}}
{{--                    <td>Sage Rodriguez</td>--}}
{{--                    <td>$56,142</td>--}}
{{--                    <td>Netherlands</td>--}}
{{--                  </tr>--}}
{{--                  <tr>--}}
{{--                    <td>4</td>--}}
{{--                    <td>Philip Chaney</td>--}}
{{--                    <td>$38,735</td>--}}
{{--                    <td>Korea, South</td>--}}
{{--                  </tr>--}}
{{--                </tbody>--}}
{{--              </table>--}}
{{--            </div>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--      </div>--}}
    </div>
  </div>
@endsection

@push('js')
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
        if ($('#dailySalesChart').length != 0 || $('#completedTasksChart').length != 0 || $('#websiteViewsChart').length != 0) {

            /* ----------==========     Daily Sales Chart initialization    ==========---------- */

            dataDailySalesChart = {
                labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
                series: [
                    [12, 17, 7, 17, 23, 18, 38]
                ]
            };

            optionsDailySalesChart = {
                lineSmooth: Chartist.Interpolation.cardinal({
                    tension: 0
                }),
                low: 0,
                high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
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

            dataCompletedTasksChart = {
                labels: ['12p', '3p', '6p', '9p', '12p', '3a', '6a', '9a'],
                series: [
                    [230, 750, 450, 300, 280, 240, 200, 190]
                ]
            };

            optionsCompletedTasksChart = {
                lineSmooth: Chartist.Interpolation.cardinal({
                    tension: 0
                }),
                low: 0,
                high: 1000, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
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

            let dataWebsiteViewsChart = {
                labels: ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'],
                series: [
                    [542, 443, 320, 780, 553, 453, 326, 434, 568, 610, 756, 895]

                ]
            };
            let optionsWebsiteViewsChart = {
                axisX: {
                    showGrid: false
                },
                low: 0,
                high: 1000,
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
                        labelInterpolationFnc: function(value) {
                            return value[0];
                        }
                    }
                }]
            ];
            let websiteViewsChart = Chartist.Bar('#websiteViewsChart', dataWebsiteViewsChart, optionsWebsiteViewsChart, responsiveOptions);

            //start animation for the Emails Subscription Chart
            md.startAnimationForBarChart(websiteViewsChart);
        }
    });
  </script>
@endpush
