@extends('layouts.admin')

@section('content-header', tr('dashboard'))

@section('breadcrumb')

<li class="breadcrumb-item active">{{tr('dashboard')}}</li>

@endsection

@section('content')

<section class="content">


    <div class="row">
        <div class="col-12 ">
            <div class="box">
              <div class="row no-gutters py-2">

                <div class="col-sm-6 col-lg-3">
                  <div class="box-body br-1 border-light">
                    <div class="flexbox mb-1">
                      <span class="font-size-18">
                        {{tr('total_posts')}}
                      </span>

                      <a href="{{ route('admin.posts.index',['user_id' => $data->user_id])}}">
                        <span class="text-primary font-size-40">{{$data->total_posts}}</span>
                      </a>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                      <div class="progress-bar bg-primary" role="progressbar" style="width: 80%; height: 4px;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>


                <div class="col-sm-6 col-lg-3 hidden-down">
                  <div class="box-body br-1 border-light">
                    <div class="flexbox mb-1">
                      <span class="font-size-18">
                        {{tr('subscription_payments')}}
                      </span>
                      <a href="{{route('admin.users_subscriptions.index',['from_user_id' => $data->user_id])}}">
                        <span class="text-info font-size-40">{{formatted_amount($data->subscription_payments)}}</span>
                      </a>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                      <div class="progress-bar bg-info" role="progressbar" style="width: 80%; height: 4px;" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>


                <div class="col-sm-6 col-lg-3 d-none d-lg-block">
                  <div class="box-body br-1 border-light">
                    <div class="flexbox mb-1">
                      <span class="font-size-18">
                        {{tr('liked_posts')}}
                      </span>
                      <a href="{{ route('admin.post_likes.index', ['user_id' => $data->user_id] ) }}">
                      <span class="text-warning font-size-40">{{$data->liked_post}}</span>
                      </a>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                      <div class="progress-bar bg-warning" role="progressbar" style="width: 80%; height: 4px;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>


                <div class="col-sm-6 col-lg-3 d-none d-lg-block">
                  <div class="box-body">
                    <div class="flexbox mb-1">
                      <span class="font-size-18">
                        {{tr('total_payments')}}
                      </span>
                      <a href="#">
                        <span class="text-danger font-size-40">{{formatted_amount($data->total_payments)}}</span>
                      </a>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                      <div class="progress-bar bg-danger" role="progressbar" style="width: 80%; height: 4px;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>


              </div>
            </div>
        </div>
        <!-- /.col -->
          
      </div>

      <div class="row">
        <div class="col-12 ">
            <div class="box">
              <div class="row no-gutters py-2">

                <div class="col-sm-6 col-lg-3 d-none d-lg-block">
                  <div class="box-body br-1 border-light">
                    <div class="flexbox mb-1">
                      <span class="font-size-18">
                        {{tr('tip_payments')}}
                      </span>
                      <a href="{{ route('admin.user_tips.index', ['user_id' => $data->user_id] ) }}">
                      <span class="text-success font-size-40">{{formatted_amount($data->user_tips)}}</span>
                      </a>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 80%; height: 4px;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>


                <div class="col-sm-6 col-lg-3 d-none d-lg-block">
                  <div class="box-body">
                    <div class="flexbox mb-1">
                      <span class="font-size-18">
                        {{tr('post_payments')}}
                      </span>
                      <a href="{{ route('admin.post.payments', ['user_id' => $data->user_id] ) }}">
                        <span class="text-danger font-size-40">{{formatted_amount($data->post_payments)}}</span>
                      </a>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                      <div class="progress-bar bg-danger" role="progressbar" style="width: 80%; height: 4px;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>


              </div>
            </div>
        </div>
        <!-- /.col -->
          
      </div>

    <div class="row match-height margin-top">

        <div class="col-xl-12 col-lg-12">

            <div class="card">
                
            <div class="card-body graph-scroll-body">

                <div class="card-header">

                    <h4 class="card-title">{{tr('recent_posts')}} - <a
                    href="{{route('admin.users.view',['user_id' => $data->user_id])}}">{{$data->user->name ?? tr('n_a')}}</a></h4>

                    <div class="heading-elements">

                        <ul class="list-inline mb-0">
                            <li>
                                <a data-action="reload"><i class="ft-rotate-cw"></i></a>
                            </li>
                            <li>
                                <a data-action="expand"><i class="ft-maximize"></i></a>
                            </li>
                        </ul>

                    </div>

                </div>

                <div class="card-content">                  
                    <div id="recent_posts" class="height-300"></div>
                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection

@section('scripts')

<script src="{{asset('admin-assets/vendors/js/charts/raphael-min.js')}}" type="text/javascript"></script>

<script src="{{asset('admin-assets/vendors/js/charts/morris.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(window).on("load", function() {

        var e = [<?php foreach ($data->analytics->last_x_days_revenues as $key => $value) {
                        echo '"' . $value->formatted_month . '"' . ',';
                    }
                    ?>
                    ];

        Morris.Area({

            element: "recent_posts",
            data: <?php print_r(json_encode($data->posts_data)); ?>,
            xkey: "month",
            ykeys: ["no_of_posts"],
            labels: ["No of Posts"],
            behaveLikeLine: !0,
            ymax: 300,
            resize: !0,
            pointSize: 0,
            pointStrokeColors: ["#00B5B8", "#FA8E57", "#F25E75"],
            smooth: !0,
            gridLineColor: "#E4E7ED",
            numLines: 6,
            gridtextSize: 14,
            lineWidth: 0,
            fillOpacity: .9,
            hideHover: "auto",
            lineColors: ["#00B5B8", "#FA8E57", "#F25E75"]
        })
    });
</script>



@endsection