@extends('layouts.admin') 

@section('title', tr('revenue_management')) 

@section('content-header', tr('revenue_management_small'))

@section('breadcrumb')

<li class="breadcrumb-item active">{{ tr('revenue_dashboard') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card revenue-dashboard-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('dashboard') }}</h4>
                    
                </div>
                
                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('revenue_dashboard_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <div class="row">
                            <div class="col-12 ">
                                <div class="box">
                                  <div class="row no-gutters py-2">
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-info">
                                          <i class="fa fa-credit-card"></i>
                                          <span class="count-numbers">
                                            <a class="count_number_a" href="{{route('admin.post.payments')}}">{{formatted_amount($data->post_payments)}}</a>
                                          </span>
                                          <span class="count-name"> {{tr('post_payments')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-primary">
                                          <i class="fa fa-credit-card"></i>
                                          <span class="count-numbers">
                                            <a class="count_number_a" href="{{route('admin.user_tips.index')}}">{{formatted_amount($data->user_tips)}}
                                            </a>
                                          </span>
                                          <span class="count-name">{{tr('tip_payments')}}</span>
                                        </div>
                                    </div>
                                        
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-success">
                                          <i class="fa fa-credit-card"></i>
                                          <span class="count-numbers">
                                            <a class="count_number_a" href="{{route('admin.users_subscriptions.index')}}">{{formatted_amount($data->subscription_payments)}}
                                            </a>
                                          </span>
                                          <span class="count-name">{{tr('subscription_payments')}}</span>
                                        </div>
                                    </div>

                                    @if(Setting::get('is_one_to_one_call_enabled'))
                                     <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-danger">
                                          <i class="fa fa-credit-card"></i>
                                          <span class="count-numbers">
                                            <a class="count_number_a" href="{{route('admin.video_call_payments.index')}}">
                                            {{formatted_amount($data->video_call_payments)}}
                                            </a></span>
                                          <span class="count-name">{{tr('video_call_payments')}}</span>
                                        </div>
                                      </div>


                                      <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-warning">
                                          <i class="fa fa-credit-card"></i>
                                          <span class="count-numbers">
                                            <a class="count_number_a" href="{{route('admin.audio_call_payments.index')}}">
                                            {{formatted_amount($data->audio_call_payments)}}
                                            </a></span>
                                          <span class="count-name">{{tr('audio_call_payments')}}</span>
                                        </div>
                                      </div>

                                    @endif

                                    @if(Setting::get('is_one_to_many_call_enabled'))
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-info">
                                          <i class="fa fa-credit-card"></i>
                                          <span class="count-numbers">
                                            <a class="count_number_a" href="{{route('admin.live_videos.payments')}}">
                                            {{formatted_amount($data->live_video_payments)}}
                                            </a></span>
                                          <span class="count-name">{{tr('live_video_payments')}}</span>
                                        </div>
                                      </div>

                                    @endif

                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-danger">
                                          <i class="fa fa-credit-card"></i>
                                          <span class="count-numbers">
                                            <a class="count_number_a" href="{{route('admin.order.payments')}}">{{formatted_amount($data->order_payments)}}</a>
                                          </span>
                                          <span class="count-name"> {{tr('order_payments')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-warning">
                                          <i class="fa fa-credit-card"></i>
                                          <span class="count-numbers">
                                            <a class="count_number_a" href="{{route('admin.chat_asset_payments.index')}}">{{formatted_amount($data->chat_asset_payments)}}</a>
                                          </span>
                                          <span class="count-name"> {{tr('chat_asset_payments')}}</span>
                                        </div>
                                    </div>

                                     <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-primary">
                                          <i class="fa fa-credit-card"></i>
                                            <span class="count-numbers">
                                            {{formatted_amount($data->today_payments)}}
                                            </span>
                                          <span class="count-name">{{tr('today_payments')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="card-counter bg-success">
                                          <i class="fa fa-credit-card"></i>
                                          <span class="count-numbers">{{formatted_amount($data->total_payments)}}
                                          </span>
                                          <span class="count-name">{{tr('total_payments')}}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                              
                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-12">

                                <!-- <div class="card card-box">

                                <div class="card-body no-padding height-9">

                                    <div class="card-head">
                                      <div class="card-header card-title">{{tr('revenues')}}</div>
                                    </div>

                                    
                                  </div> -->
                                    <div class="card-header">

                                        <h4 class="card-title">{{ tr('revenues') }} - {{tr('post_payments')}}</h4>
                                        
                                    </div><br>

                                    <div class="card-body">
                                     <p class="text-muted">{{tr('post_payments_analytics')}}(in {{Setting::get('token_symbol')}})</p>
                                  </div>
                                  <canvas id="bar-chart"></canvas>

                                </div>

                            </div>
                            
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</section>


@endsection 

@section('scripts')

    <!-- <script src="{{asset('admin-assets/dashboard-assets/assets/plugins/chart-js/Chart.bundle.js')}}" ></script>

    <script src="{{asset('admin-assets/dashboard-assets/assets/plugins/chart-js/utils.js')}}" ></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

    <!-- <script type="text/javascript">

        $(document).ready(function() {

            new Chart(document.getElementById("bar-chart"), {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($data->analytics->last_x_days_revenues as $key => $value)       {
                                echo '"'.$value->date.'"'.',';
                            } 
                            ?>],
                    datasets: [
                               {
                                   label: "Post Earnings",
                                   backgroundColor: "#3e95cd",
                                   data:[<?php 
                                            foreach ($data->analytics->last_x_days_revenues as $value) {
                                                echo $value->total_post_earnings.',';
                                            }

                                        ?>]
                                    
                               }, 

                               {
                                   label: "Subscription Earnings",
                                   backgroundColor: "#8e5ea2",
                                   data: [<?php 
                                            foreach ($data->analytics->last_x_days_revenues as $value) {
                                                echo $value->total_subscription_earnings.',';
                                            }

                                        ?>]
                               }
                               ]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Total Post Earnings (in {{Setting::get('currency')}})'
                    }
                }
            });
        });

        </script> -->
        <script>
             if ($('#bar-chart').length) {
                new Chart($("#bar-chart"), {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($data->analytics->last_x_days_revenues as $key => $value)       {
                                echo '"'.$value->date.'"'.',';
                            } 
                            ?>],
                    datasets: [{
                        label: "Post Earnings",
                        backgroundColor: ["#b1cfec", "#7ee5e5", "#66d1d1", "#f77eb9", "#4d8af0", "#b1cfec", "#7ee5e5", "#66d1d1", "#f77eb9", "#4d8af0"],
                        data: [<?php 
                                foreach ($data->analytics->last_x_days_revenues as $value) {
                                    echo $value->total_post_earnings.',';
                                }

                            ?>]
                    },
                    {
                        label: "Subscription Earnings",
                        backgroundColor: ["#b1cfec", "#7ee5e5", "#66d1d1", "#f77eb9", "#4d8af0", "#b1cfec", "#7ee5e5", "#66d1d1", "#f77eb9", "#4d8af0"],
                        data: [<?php 
                                foreach ($data->analytics->last_x_days_revenues as $value) {
                                    echo $value->total_subscription_earnings.',';
                                }

                            ?>]
                    }]
                },
                options: {
                    legend: { display: false },
                }
            });
    }
        </script>

@endsection
