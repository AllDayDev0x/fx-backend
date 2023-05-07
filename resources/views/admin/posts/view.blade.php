@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')



<li class="breadcrumb-item active"><a href="{{route('admin.posts.index')}}">{{tr('posts')}}</a>
</li>

<li class="breadcrumb-item">{{tr('view_posts')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="col-12">

        <div class="card post-view-personal-bio-sec">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('view_posts') }}</h4>
                <a class="heading-elements-toggle"></a>

            </div>

            <div class="card-body">

                <div class="row">


                    <div class="col-xl-2 col-lg-2 col-md-12 resp-mrg-btm-xs">

                        <img src="{{$post->user->picture ?? asset('placeholder.jpeg')}}" class="post-image" alt="Card image" />

                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-12 resp-mrg-btm-xs">

                        <h4 class="card-title">{{$post->user->name ?? "-"}}</h4><br>

                        <h6 class="card-subtitle text-muted ml-4">{{$post->user->email ?? "-"}}</h6>
                        <br>

                        <a href="{{route('admin.users.view',['user_id' => $post->user_id])}}" class="btn btn-primary">
                            {{tr('go_to_profile')}}
                        </a>

                        @if($post->is_paid_post == YES)

                        <a href="{{route('admin.post.payments',['post_id'=>$post->id])}}" class="btn btn-purple">{{tr('post_payments')}}</a>

                        @endif

                    </div>

                    <div class="col-xl-6 col-md-6 col-lg-6">

                        <h4 class="card-title">{{tr('action')}}</h4><br>

                        @if(Setting::get('is_demo_control_enabled') == YES)

                        <a class="btn-sm btn-danger ml-4" href="javascript:void(0)">{{ tr('delete') }}</a>

                        @else

                                    <div class="px-2 resp-marg-top-xs">

                                        <div class="row">

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-success btn-block btn-min-width mr-1 mb-1 " href="{{ route('admin.posts.dashboard', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('dashboard') }}</a>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.posts.edit', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                            @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                            @else

                                                <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{ tr('post_delete_confirmation' , $post->unique_id) }}&quot;);" href="{{ route('admin.posts.delete', ['post_id' => $post->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                            @endif
                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                @if($post->status == APPROVED)

                                                <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" href="{{  route('admin.posts.status' , ['post_id' => $post->id] )  }}" onclick="return confirm(&quot;{{ tr('post_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="btn btn-info btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.posts.status' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                            </div>

                                        

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-info btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.posts.comments' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('comments') }}</a>

                                            </div>


                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                @if($post->is_published == NO)

                                                <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.posts.publish' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('publish') }}</a>

                                                @endif

                                            </div>

                                        </div>

                        

                            @endif

                            @if($post->status == APPROVED)

                            <!-- <a class="btn-sm btn-secondary" href="{{  route('admin.posts.status' , ['post_id' => $post->id] )  }}" onclick="return confirm(&quot;{{ tr('post_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                            </a> -->

                            @else

                           <!--  <a class="btn-sm btn-info" href="{{ route('admin.posts.status' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('approve') }}</a> -->

                            @endif
                        </div>

                    </div>

                </div>
                <hr>

                @if($post->is_paid_post == PAID)

                    <div class="row">

                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="info-box bg-purple">
                                <span class="info-box-icon bg-white text-purple"><i class="ion ion-card"></i></span>

                                <div class="info-box-content">
                                <span class="info-box-number">{{formatted_amount($payment_data->total_earnings)}}</span>
                                <span class="info-box-text">{{tr('total_earnings')}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                    </div>

                    <div class="col-xl-4 col-md-6 col-12">
                            <div class="info-box bg-red">
                                <span class="info-box-icon bg-white text-red"><i class="ion ion-card"></i></span>

                                <div class="info-box-content">
                                <span class="info-box-number">{{formatted_amount($payment_data->current_month_earnings)}}</span>
                                <span class="info-box-text">{{tr('current_month_earnings')}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                    </div>

                    <div class="col-xl-4 col-md-6 col-12">
                            <div class="info-box bg-yellow">
                                <span class="info-box-icon bg-white text-yellow"><i class="ion ion-card"></i></span>

                                <div class="info-box-content">
                                <span class="info-box-number">{{formatted_amount($payment_data->today_earnings)}}</span>
                                <span class="info-box-text">{{tr('today_earnings')}}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                    </div>

                    </div>

                @endif

                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-12">

                        <div class="box box-outline-purple">

                            <div class="box-body">

                                <ul class="post-left ml-4">
                                    <li><b>{{tr('post_id')}}</b> - {{$post->unique_id}}</li>
                                    <hr>

                                    <li><b>{{tr('publish_time')}}</b> - {{common_date($post->publish_time , Auth::guard('admin')->user()->timezone)}}</li>
                                    <hr>

                                    <li><b>{{tr('is_paid_post')}}</b> - @if($post->is_paid_post)
                                        <span class="badge badge-info">{{tr('yes')}}</span>
                                        @else
                                        <span class="badge badge-danger">{{tr('no')}}</span>
                                        @endif
                                    </li>
                                    <hr>

                                    <li><b>{{tr('amount')}}</b> - {{$post->amount_formatted ?: 0}}</li>
                                    <hr>

                                    <li><b>{{tr('total_likes')}}</b> - {{$payment_data->likes ?: 0}}</li>
                                    <hr>

                                    <li><b>{{tr('post_type')}}</b> - {{$post_files[0]->file_type ?? tr('text')}}</li>
                                    <hr>

                                    <li><b>{{tr('total_post_bookmarks')}}</b> - {{$payment_data->total_bookmarks ?: 0}}</li>
                                    <hr>
                                    <li><b>{{tr('categories')}}</b> - 

                                        @if(count($categories)>0)

                                            @foreach($categories as $i => $category)

                                                @if(count($categories) == $i+1)

                                                {{$category}}

                                                @else

                                                {{$category}},

                                                @endif

                                            @endforeach

                                        @else

                                            {{tr('n_a')}}

                                        @endif
                                    </li>

                                </ul>
                            </div>

                        </div>

                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-12">

                        <div class="box box-outline-purple">

                            <div class="box-body">

                                <ul>
                                    <li><b>{{tr('content')}}</b> - </li>
                                     <div style="height: 150px; overflow:auto;">{!! $post->content ?: tr('n_a') !!}
                                        </div>
                                    
                                    <hr>

                                    <li><b>{{tr('status')}}</b> -

                                        @if($post->status == APPROVED)

                                        <span class="badge badge-info">{{ tr('approved') }}</span>
                                        @else

                                        <span class="badge badge-danger">{{ tr('declined') }}</span>
                                        @endif
                                    </li>
                                    <hr>

                                    <li><b>{{tr('created_at')}}</b> - {{common_date($post->created_at , Auth::guard('admin')->user()->timezone)}}</li>
                                    <hr>

                                    <li><b>{{tr('updated_at')}}</b> - {{common_date($post->updated_at , Auth::guard('admin')->user()->timezone)}}</li>

                                    </li>
                                </ul>
                            </div>

                        </div>

                    </div>

                </div>


                <hr>

                @if(count($post_files)>0)

                <div class="row">

                    <div class="col-lg-6 col-12">

                        <div class="box">
                            <div class="box-header with-border">
                                  <h3 class="box-title">{{tr('post')}}</h3>
                            </div>
                            <div class="box-body">

                                @if($post_files[0]->file_type == POSTS_VIDEO || $post_files[0]->file_type == POSTS_IMAGE)

                                    <ul class="bo-slider">

                                        @foreach($post_files as $i => $post_file)

                                            <li data-url="{{ asset($post_file->file)}}" data-type="{{$post_file->file_type}}"></li>

                                        @endforeach
                                        
                                    </ul>

                                @else

                                    <div id="carousel-example-generic-captions" class="carousel slide" data-interval="false" data-ride="carousel">

                                        <div class="carousel-inner" role="listbox">
                                            @foreach($post_files as $i => $post_file)

                                                <div class="carousel-item {{$i==0 ? 'active' : ''}}">

                                                    <audio id="audio{{$i}}" class="col-12" controls controlsList="nodownload">
                                                        <source src="{{ asset($post_file->file)}}" class="img-fluid" alt="{{tr('post')}}" type="audio/mpeg">
                                                    </audio>

                                                </div>

                                            @endforeach

                                            <ol class="carousel-indicators">

                                                <button id="left_button" onclick="change(-1)" class="carousel-control" style="color:blue;font-size:40px;" href="#carousel-example-generic-captions" data-slide="prev">&lsaquo;</button>
                                                <button id="right_button" onclick="change(+1)" class="carousel-control" style="color:blue;font-size:40px;text-align:right;"  href="#carousel-example-generic-captions" data-slide="next">&rsaquo;</button>

                                            </ol>

                                        </div>

                                    </div>

                                @endif
                                

                            </div>

                        </div>

                    </div>
                    @foreach($post_files as $i => $post_file)

                        @if($post_file->file_type == POSTS_VIDEO)

                            <div class="col-lg-6 col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        @if ($loop->first)
                                          <h3 class="box-title">{{tr('preview_file')}}</h3>
                                        @endif
                                    </div>
                                    
                                     <div class="box-body">
                                        <div>
                                            @if ($loop->first)
                                               <img style="width:400px;" src="{{ asset($post_file->preview_file)}}" class="img-fluid" alt="{{tr('preview_image')}}">
                                            @endif
                                        </div>
                                       
                                    </div>
                                </div>

                            </div>

                            @if($post_file->video_preview_file)
                            <div class="col-lg-6 col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        @if ($loop->first)
                                          <h3 class="box-title">{{tr('preview_video')}}</h3>
                                        @endif
                                    </div>
                                    
                                     <div class="box-body">
                                        <div>
                                            @if ($loop->first)
                                                <video width="400" controls controlsList="nodownload">
                                                    <source src="{{$post_file->video_preview_file}}" type="video/mp4" class="img-fluid" alt="{{tr('post')}}">
                                                </video>

                                            @endif
                                        </div>
                                       
                                    </div>
                                </div>

                            </div>
                            @endif

                        @endif

                    @endforeach

                </div>

                @endif

            </div>

        </div>

    </div>

</div>

</section>

@endsection

@section('scripts')

    <!-- Bootstrap slider -->
    <script src="{{asset('admin-assets/js/jquery.slimscroll.min.js')}}"></script>

    <script src="{{asset('admin-assets/bootstrap-slider/bootstrap-slider.js')}}" type="text/javascript"></script>

    <script src="{{asset('admin-assets/bootstrap-slider/script.min.js')}}" type="text/javascript"></script>

    <!-- <style>
        html,* { margin: 0; padding: 0; }
        .container { margin: 150px auto;  }
    </style> -->
    
    <script>
      $(function () {
        /* BOOTSTRAP SLIDER */
        $('.slider').slider()
      });

    </script>

    <script type="text/javascript">
        $('.bo-slider').boSlider({
            slideShow: false,
            interval: 3000,
            // animation: "fade"
        });

        $( document ).ready(function() {
            $('#left_button').fadeTo("slow", 0.33);
            $('#left_button').prop('disabled', true);

        });

        function change(i){

            var currentIndex = $('div.active').index() + i;
            
            var previous = 'audio'+(currentIndex-1);

            if (document.getElementById(previous)) {
                var previous = document.getElementById(previous);
                previous.pause();
            }

            var after = 'audio'+(currentIndex+1);

            if (document.getElementById(after)) {
                var after = document.getElementById(after);
                after.pause();
            }

            var present = 'audio'+(currentIndex);

            if (document.getElementById(present)) {
                var present = document.getElementById(present);
                present.pause();
            }

        }

        $('.carousel').carousel({
          wrap: false
        }).on('slid.bs.carousel', function () {
            curSlide = $('.active');
          if(curSlide.is( ':first-child' )) {

             $('#left_button').fadeTo("slow", 0.33);
             $('#left_button').prop('disabled', true);
          } else {
             $('#left_button').fadeTo("slow", 0.8);
             $('#left_button').prop('disabled', false);
          }

          var totalItems = $('.carousel-item').length;
          var currentIndex = $('div.active').index() + 1;
          
          if (totalItems == currentIndex) {
             $('#right_button').fadeTo("slow", 0.33);
             $('#right_button').prop('disabled', true);
          } else {
             $('#right_button').fadeTo("slow", 0.8);  
             $('#right_button').prop('disabled', false);
          }
        });

    </script>

@endsection