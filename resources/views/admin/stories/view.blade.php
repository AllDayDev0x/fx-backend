@extends('layouts.admin')

@section('title', tr('stories'))

@section('content-header', tr('stories'))

@section('breadcrumb')



<li class="breadcrumb-item active"><a href="{{route('admin.stories.index')}}">{{tr('stories')}}</a>
</li>

<li class="breadcrumb-item">{{tr('view_stories')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="col-12">

        <div class="card post-view-personal-bio-sec">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('view_stories') }}</h4>
                <a class="heading-elements-toggle"></a>

            </div>

            <div class="card-body">

                <div class="row">


                    <div class="col-xl-2 col-lg-2 col-md-12 resp-mrg-btm-xs">

                        <img src="{{$story->user->picture ?? asset('placeholder.jpeg')}}" class="story-image" alt="Card image" />

                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-12 resp-mrg-btm-xs">

                        <h4 class="card-title">{{$story->user->name ?? "-"}}</h4><br>

                        <h6 class="card-subtitle text-muted ml-4">{{$story->user->email ?? "-"}}</h6>
                        <br>

                        <a href="{{route('admin.users.view',['user_id' => $story->user_id])}}" class="btn btn-primary ml-4">
                            {{tr('go_to_profile')}}
                        </a>

                    </div>

                    <div class="col-xl-6 col-md-6 col-lg-6">

                        <h4 class="card-title">{{tr('action')}}</h4><br>

                        @if(Setting::get('is_demo_control_enabled') == YES)

                        <a class="btn-sm btn-danger ml-4" href="javascript:void(0)">{{ tr('delete') }}</a>

                        @else

                                    <div class="px-2 resp-marg-top-xs">

                                        <div class="row">

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.stories.edit', ['story_id' => $story->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                            @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                            @else

                                                <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{ tr('story_delete_confirmation' , $story->unique_id) }}&quot;);" href="{{ route('admin.stories.delete', ['story_id' => $story->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                            @endif
                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                @if($story->status == APPROVED)

                                                <a class="btn btn-info btn-block btn-min-width mr-1 mb-1" href="{{  route('admin.stories.status' , ['story_id' => $story->id] )  }}" onclick="return confirm(&quot;{{ tr('story_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="btn btn-success btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.stories.status' , ['story_id' => $story->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                            </div>

                                        </div>

                        

                            @endif

                        </div>

                    </div>

                </div>
                <hr>

                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-12">

                        <div class="box box-outline-purple">

                            <div class="box-body">

                                <ul class="post-left ml-4">

                                    <li class="text-uppercase">{{tr('story_id')}} - {{$story->unique_id}}</li>

                                    <hr>

                                    <li>{{tr('publish_time')}} - {{common_date($story->publish_time , Auth::guard('admin')->user()->timezone)}}</li>
                        
                                    <hr>

                                    <li>{{tr('username')}} - {{$story->user_displayname ?: tr('n_a')}}</li>
                                    <hr>

                                    <li>{{tr('story_type')}} - {{$story_files[0]->file_type ?? tr('text')}}</li>
                                    <hr>
                                </ul>
                            </div>

                        </div>

                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-12">

                        <div class="box box-outline-purple">

                            <div class="box-body">

                                <ul>
                                    <li>{{tr('content')}} - {{$story->content ?: tr('n_a')}}</li>
                                    <hr>

                                    <li>{{tr('status')}} -

                                        @if($story->status == APPROVED)

                                        <span class="badge badge-success">{{ tr('approved') }}</span>
                                        @else

                                        <span class="badge badge-danger">{{ tr('declined') }}</span>
                                        @endif
                                    </li>
                                    <hr>

                                    <li>{{tr('created_at')}} - {{common_date($story->created_at , Auth::guard('admin')->user()->timezone)}}</li>
                                    <hr>

                                    <li>{{tr('updated_at')}} - {{common_date($story->updated_at , Auth::guard('admin')->user()->timezone)}}</li>
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
                                  <h3 class="box-title">{{tr('story')}}</h3>
                            </div>
                            <div class="box-body">
                                <div id="carousel-example-generic-captions" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner" role="listbox">

                                        <ol class="carousel-indicators">

                                        @foreach($story_files as $i => $story_file)

                                                <li data-target="#carousel-example-generic-captions" data-slide-to="
                                                {{$i}}" class=" {{$i==0 ? 'active' : ''}}"></li>
                                            
                                        @endforeach

                                        </ol>

                                        @foreach($story_files as $i => $story_file)

                                                @if($story_file->file_type == STORY_VIDEO)

                                                    <div class="carousel-item {{$i==0 ? 'active' : ''}}">
                                                        <video width="400" controls>
                                                          <source src="{{ asset($story_file->file)}}" type="video/mp4" class="img-fluid" alt="{{tr('story')}}">
                                                        </video>
                                                    </div>

                                                @else

                                                    <div class="carousel-item {{$i==0 ? 'active' : ''}}">
                                                        <img width="400" src="{{ asset($story_file->file)}}" class="img-fluid" alt="{{tr('story')}}">
                                                    </div>

                                                @endif
                
                                        @endforeach

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    @foreach($story_files as $i => $story_file)

                        @if($story_file->file_type == STORY_VIDEO)

                            <div class="col-lg-6 col-12" style="display:none;">
                                <div class="box">
                                    <div class="box-header with-border">
                                        @if ($loop->first)
                                          <h3 class="box-title">{{tr('preview_file')}}</h3>
                                        @endif
                                    </div>
                                    
                                     <div class="box-body">
                                        <div>
                                            @if ($loop->first)
                                               <img style="width:400px;" src="{{ asset($story_file->preview_file)}}" class="img-fluid" alt="{{tr('preview_image')}}">
                                            @endif
                                        </div>
                                       
                                    </div>
                                </div>

                            </div>

                        @endif

                    @endforeach

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