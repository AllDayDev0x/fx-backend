@extends('layouts.admin')

@section('content-header', tr('dashboard'))

@section('breadcrumb')

<li class="breadcrumb-item active">{{tr('dashboard')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">
        <div class="col-12 ">
          <div class="callout bg-pale-secondary" style="background:#fff;">
           <h4>{{tr('notes')}}</h4>
           <p>
           </p><ul>
            <li>
              {{tr('dashboard_notes')}}
            </li>
          </ul>
          <p></p>
        </div>
        <div class="box">
            <div class="row no-gutters py-2">
               <div class="col-md-3 col-sm-6">
                <div class="card-counter bg-info">
                  <i class="fa fa-users"></i>
                  <span class="count-numbers">
                    <a class="count_number_a" href="{{route('admin.users.index')}}">{{$data->total_users ?: tr('n_a')}}</a>
                  </span>
                  <span class="count-name"> {{tr('total_users')}}</span>
                </div>
              </div>

              <div class="col-md-3 col-sm-6">
                <div class="card-counter bg-primary">
                  <i class="fa fa-database"></i>
                  <span class="count-numbers">
                    <a class="count_number_a" href="{{route('admin.users.index', ['account_type' => USER_PREMIUM_ACCOUNT])}}">{{$data->total_premium_users ?: tr('n_a')}}
                    </a>
                  </span>
                  <span class="count-name">{{tr('premium_users')}}</span>
                </div>
              </div>

              <div class="col-md-3 col-sm-6">
                <div class="card-counter bg-success">
                  <i class="fa fa-newspaper-o"></i>
                  <span class="count-numbers">
                    <a class="count_number_a" href="{{route('admin.posts.index')}}">{{$data->total_posts ?: tr('n_a')}}
                    </a>
                  </span>
                  <span class="count-name">{{tr('total_posts')}}</span>
                </div>
              </div>

              <div class="col-md-3">
                <div class="card-counter bg-danger">
                  <i class="fa fa-money"></i>
                  <span class="count-numbers">{{formatted_amount($data->total_revenue) ?: 0}}</span>
                  <span class="count-name">{{tr('total_revenue')}}</span>
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

          <h4 class="card-title">{{tr('recent_posts')}}</h4>

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
         <div class="card-body">
             <p class="text-muted">{{tr('recent_posts_statastics_notes')}}</p>
          </div>
        <div class="card-content">                  
          <div id="recent_posts" class="height-300"></div>
        </div>

      </div>

    </div>

  </div>

</div>

<div class="row">
  <div class="col-lg-6 col-12">
    <div class="box">
      <div class="box-header with-border">
        <h5 class="box-title">{{tr('recent_users')}}</h5>
      </div>
      <div class="box-body p-0">
        <div class="media-list media-list-hover media-list-divided inner-user-div" style="height: 345px"><hr>
          @forelse($data->recent_users as $i => $user)

          <a href="{{route('admin.users.view',['user_id'=>$user->id])}}">

            <div class="media media-single">
             <!-- <a href="{{route('admin.users.view',['user_id'=>$user->id])}}"> -->
              <img class="avatar" src="{{$user->picture}}" alt="...">
            <!-- </a> -->

            <div class="media-body">
              <h6> {{ $user->name ?: tr('n_a')}}</h6>
              <small class="text-fader">{{$user->created_at ?: tr('n_a')}}</small>
            </div>

            <!-- <div class="media-right">
              <a class="btn btn-block btn-default btn-sm" href="{{route('admin.users.view',['user_id'=>$user->id])}}">{{tr('view')}}</a>
            </div> -->
          </div>

        </a><hr>
        @empty

        <div class="text-center m-5">
          <h2 class="text-muted">
            <i class="fa fa-inbox"></i>
          </h2>
          <p>{{tr('no_result_found')}}</p>
        </div>


        @endforelse

      </div>


    </div>
    <div class="text-center bt-1 border-light p-2">
      <a class="text-uppercase d-block font-size-12" href="{{route('admin.users.index')}}">{{tr('view_all')}}</a>
    </div>
  </div>

</div>



<div class="col-lg-6 col-12">
  <div class="box">
    <div class="box-header with-border">
      <h5 class="box-title">{{tr('recent_premium_users')}}</h5>
    </div>
    <div class="box-body p-0">
      <div class="media-list media-list-hover media-list-divided inner-user-div" style="height: 345px"><hr>
        @forelse($data->recent_premium_users as $i => $recent_premium_user)

        <a href="{{route('admin.users.view',['user_id'=>$recent_premium_user->id])}}">

          <div class="media media-single">
           <!-- <a href="{{route('admin.users.view',['user_id'=>$recent_premium_user->id])}}"> -->
            <img class="avatar" src="{{$recent_premium_user->picture}}" alt="...">
          <!-- </a> -->

          <div class="media-body">
            <h6> {{ $recent_premium_user->name ?: tr('n_a') }}</h6>
            <small class="text-fader">{{$recent_premium_user->created_at ?: tr('n_a')}}</small>
          </div>

          <!-- <div class="media-right">
            <a class="btn btn-block btn-default btn-sm" href="{{route('admin.users.view',['user_id'=>$recent_premium_user->id])}}">{{tr('view')}}</a>
          </div> -->
        </div>
      
      </a><hr>
      @empty

      <div class="text-center m-5">
        <h2 class="text-muted">
          <i class="fa fa-inbox"></i>
        </h2>
        <p>{{tr('no_result_found')}}</p>
      </div>


      @endforelse

    </div>


  </div>
  <div class="text-center bt-1 border-light p-2">
    <a class="text-uppercase d-block font-size-12" href="{{route('admin.users.index',['account_type'=>USER_PREMIUM_ACCOUNT])}}">{{tr('view_all')}}</a>
  </div>
</div>

</div>
</div>

<div class="row">
  <div class="col-lg-6 col-12">
    <div class="box">
      <div class="box-header with-border">
        <h5 class="box-title">{{tr('recent_post')}}</h5>
      </div>
      <div class="box-body p-0">
        <div class="media-list media-list-hover media-list-divided inner-user-div" style="height: 345px"><hr>
          @forelse($data->recent_posts as $i => $post)

          <a  href="{{route('admin.posts.view',['post_id'=>$post->id])}}">

            <div class="media media-single">
                <img class="avatar" src="{{$post->user->picture ?? asset('placeholder.jpeg')}}" alt="...">
              

              <div class="media-body" style="height: 45px; overflow: hidden;">
                <h6>{!! $post->content ?: tr('n_a') !!}</h6>
                <small class="text-fader">{{$user->created_at ?: tr('n_a')}}</small>
              </div>

              <!-- <div class="media-right">
                <a class="btn btn-block btn-default btn-sm" href="{{route('admin.posts.view',['post_id'=>$post->id])}}">{{tr('view')}}</a>
              </div> -->
            </div>

          </a><hr>
          @empty

          <div class="text-center m-5">
            <h2 class="text-muted">
              <i class="fa fa-inbox"></i>
            </h2>
            <p>{{tr('no_result_found')}}</p>
          </div>


          @endforelse

        </div>

      </div>
      <div class="text-center bt-1 border-light p-2">
        <a class="text-uppercase d-block font-size-12" href="{{route('admin.posts.index')}}">{{tr('view_all')}}</a>
      </div>
    </div>
    
  </div>
  <div class="col-lg-6 col-12">
    <div class="box">
      <div class="box-header with-border">
        <h5 class="box-title">{{tr('recent_post_payments')}}</h5>
      </div>
      <div class="box-body p-0">
        <div class="media-list media-list-hover media-list-divided inner-user-div" style="height: 345px"><hr>
          @forelse($data->recent_post_payments as $i => $post_payment)

          <a href="{{route('admin.post.payments.view',['post_payment_id'=>$post_payment->id])}}">

            <div class="media media-single">
              
              <img class="avatar" src="{{$post_payment->user->picture ?? asset('placeholder.jpeg')}}" alt="...">
              

              <div class="media-body">
                <h6> {{ $post_payment->user->name ?? tr('n_a') }}</h6>
                <small class="text-fader">{{$post_payment->created_at ?: tr('n_a')}}</small>
              </div>

              <!-- <div class="media-right">
                <a class="btn btn-block btn-default btn-sm" href="{{route('admin.post.payments.view',['post_payment_id'=>$post_payment->id])}}">{{tr('view')}}</a>
              </div> -->
            </div>

          </a><hr>
          @empty

          <div class="text-center m-5">
            <h2 class="text-muted">
              <i class="fa fa-inbox"></i>
            </h2>
            <p>{{tr('no_result_found')}}</p>
          </div>


          @endforelse

        </div>


      </div>
      <div class="text-center bt-1 border-light p-2">
        <a class="text-uppercase d-block font-size-12" href="{{route('admin.post.payments')}}">{{tr('view_all')}}</a>
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