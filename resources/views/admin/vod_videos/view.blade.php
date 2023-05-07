@extends('layouts.admin')

@section('title', tr('view_vod'))

@section('content-header', tr('view_vod'))

@section('breadcrumb')



<li class="breadcrumb-item active"><a href="{{route('admin.vod_videos.index')}}">{{tr('vods')}}</a>
</li>

<li class="breadcrumb-item">{{tr('view_vod')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="col-12">

        <div class="card post-view-personal-bio-sec">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('view_vod') }}</h4>
                <a class="heading-elements-toggle"></a>

            </div>

            <div class="card-body">

                <div class="row">


                    <div class="col-xl-2 col-lg-2 col-md-12 resp-mrg-btm-xs">

                        <img src="{{$vod_video->user->picture ?? asset('placeholder.jpeg')}}" class="vod-image" alt="Card image" />

                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-12 resp-mrg-btm-xs">

                        <h4 class="card-title">{{$vod_video->user->name ?? tr('n_a')}}</h4><br>

                        <h6 class="card-subtitle text-muted ml-4">{{$vod_video->user->email ?? tr('n_a')}}</h6>
                        <br>

                        <a href="{{route('admin.users.view',['user_id' => $vod_video->user_id])}}" class="btn btn-primary ml-4">
                            {{tr('go_to_profile')}}
                        </a>

                    </div>

                    <div class="col-xl-6 col-md-12 col-lg-6">

                        <h4 class="card-title">{{tr('vod')}}</h4><br>

                        @if(Setting::get('is_demo_control_enabled') == YES)

                        <a class="btn-sm btn-danger ml-4" href="javascript:void(0)">{{ tr('delete') }}</a>

                        @else
                        <div class="px-2 resp-marg-top-xs">

                                        <div class="row">

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-info btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.vod_videos.edit', ['vod_id' => $vod_video->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                            @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                            @else

                                                <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{ tr('vod_delete_confirmation' , $vod_video->unique_id) }}&quot;);" href="{{ route('admin.vod_videos.delete', ['vod_id' => $vod_video->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                            @endif
                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                @if($vod_video->status == APPROVED)

                                                <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" href="{{  route('admin.vod_videos.status' , ['vod_id' => $vod_video->id] )  }}" onclick="return confirm(&quot;{{ tr('vod_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="btn btn-info btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.vod_videos.status' , ['vod_id' => $vod_video->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                            </div>

                                        </div>

                        

                            @endif
                    </div></div>

                </div>
                <hr>

                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-12">

                        <div class="box box-outline-purple">

                            <div class="box-body">

                                <ul class="post-left ml-4">
                                    <li class="text-uppercase"><b>{{tr('unique_id')}}</b> - {{$vod_video->unique_id ?: tr('n_a')}}</li>
                                    <hr>

                                    <li><b>{{tr('publish_time')}}</b> - {{common_date($vod_video->publish_time , Auth::guard('admin')->user()->timezone)}}</li>
                        
                                    <hr>

                                    <li><b>{{tr('username')}}</b> - {{$vod_video->user_displayname ?: tr('n_a')}}</li>
                                    <hr>

                                </ul>
                            </div>

                        </div>

                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-12">

                        <div class="box box-outline-purple">

                            <div class="box-body">

                                <ul>
                                    <li><b>{{tr('description')}}</b> - {{$vod_video->description ?: tr('n_a')}}</li>
                                    <hr>

                                    <li><b>{{tr('status')}}</b> -

                                        @if($vod_video->status == APPROVED)

                                        <span class="badge badge-info">{{ tr('approved') }}</span>
                                        @else

                                        <span class="badge badge-danger">{{ tr('declined') }}</span>
                                        @endif
                                    </li>
                                    <hr>

                                    <li><b>{{tr('created_at')}}</b> - {{common_date($vod_video->created_at , Auth::guard('admin')->user()->timezone)}}</li>
                                    <hr>

                                    <li><b>{{tr('updated_at')}}</b> - {{common_date($vod_video->updated_at , Auth::guard('admin')->user()->timezone)}}</li>
                                    <hr>
                                </ul>
                            </div>

                        </div>

                    </div>

                </div>


                <hr>
                

                <div class="row">

                    <div class="col-lg-6 col-12">

                        <div class="box">
                            <div class="box-header with-border">
                                  <h3 class="box-title">{{tr('vod')}}</h3>
                            </div>
                            <div class="box-body">
                                <div id="carousel-example-generic-captions" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner" role="listbox">

                                        <ol class="carousel-indicators">

                                        @foreach($vod_files as $i => $vod_file)

                                                <li data-target="#carousel-example-generic-captions" data-slide-to="
                                                {{$i}}" class=" {{$i==0 ? 'active' : ''}}"></li>
                                            
                                        @endforeach

                                        </ol>

                                        @foreach($vod_files as $i => $vod_file)

                                            <div class="carousel-item {{$i==0 ? 'active' : ''}}">
                                                <video width="400" controls>
                                                    <source src="{{ asset($vod_file->file)}}" type="video/mp4" class="img-fluid" alt="{{tr('vod')}}">
                                                </video>
                                            </div>
                
                                        @endforeach

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-6 col-12">

                        <div class="box">
                            <div class="box-header with-border">
                                  <h3 class="box-title">{{tr('preview_image')}}</h3>
                            </div>
                            <div class="box-body">
                                
                                <div>
                                    <img style="width:400px;" src="{{ asset($vod_file->preview_file)}}" class="img-fluid" alt="{{tr('preview_image')}}">
                                </div>

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

    <!-- Bootstrap slider -->
    <script src="{{asset('admin-assets/js/jquery.slimscroll.min.js')}}"></script>

    <script src="{{asset('admin-assets/bootstrap-slider/bootstrap-slider.js')}}" type="text/javascript"></script>
    
    <script>
      $(function () {
        /* BOOTSTRAP SLIDER */
        $('.slider').slider()
      })
    </script>

@endsection