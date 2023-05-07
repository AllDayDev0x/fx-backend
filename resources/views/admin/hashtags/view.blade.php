@extends('layouts.admin')

@section('content-header', tr('hashtags'))

@section('breadcrumb')

    <li class="breadcrumb-item active">
        <a href="{{route('admin.hashtags.index')}}">{{ tr('hashtags') }}</a>
    </li>

    <li class="breadcrumb-item">{{tr('view_hashtags')}}</li>

@endsection

@section('content')

<section class="content">

        <div class="row">

            <div class="col-xl-12 col-lg-12">

                <div class="card user-profile-view-sec">

                    <div class="card-header border-bottom border-gray">

                        <h4 class="card-title">{{ tr('view_hashtags') }}</h4>

                    </div>

                    <div class="card-content">

                        <div class="user-view-padding">
                            <div class="row"> 

                                <div class=" col-xl-6 col-lg-6 col-md-12">
                                    <div class="table-responsive">

                                        <table class="table table-xl mb-0">
                                            <tr >
                                                <th>{{tr('hashtag_id')}}</th>
                                                <td>{{$hashtag->unique_id ?? tr('n_a')}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ tr('hashtag_name') }}</th>
                                                <td>{{ $hashtag->name ?: tr('n_a') }}</td>
                                            </tr>
                                             <tr>
                                                <th>{{ tr('total_posts') }}</th>
                                                <td>
                                                    <a href="{{ route('admin.posts.index', ['hashtag_id' => $hashtag->id] ) }}">
                                                        {{$hashtag->postHashtag->count() ?? 0}}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('status')}}</th>
                                                <td>
                                                    @if($hashtag->status == APPROVED)

                                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>
                                                    @else

                                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('created_at')}} </th>
                                                <td>{{common_date($hashtag->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('updated_at')}} </th>
                                                <td>{{common_date($hashtag->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr> 

                                            <tr>
                                                <th>{{tr('description')}}</th>
                                                <td>{!!$hashtag->description ?: tr('n_a')!!}</td>
                                            </tr> 
                                        </table>

                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-12">

                                    <div class="px-2 resp-marg-top-xs">

                                        <div class="card-title">{{tr('action')}}</div>

                                        <div class="row">

                                            @if(Setting::get('is_demo_control_enabled') == YES)

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-secondary btn-block btn-min-width mr-1 mb-1 " href="javascript:void(0)"> &nbsp;{{tr('edit')}}</a>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" href="javascript:void(0)">&nbsp;{{tr('delete')}}</a>

                                            </div>


                                            @else

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('hashtag_delete_confirmation' , $hashtag->name)}}&quot;);" href="{{ route('admin.hashtags.delete', ['hashtag_id' => $hashtag->id,'page'=>request()->input('page')] ) }}">&nbsp;{{tr('delete')}}</a>

                                            </div>

                                            @endif

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                @if($hashtag->status == APPROVED)
                                                     

                                                    <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" href="{{  route('admin.hashtags.status' , ['hashtag_id' => $hashtag->id] )  }}" onclick="return confirm(&quot;{{ tr('hashtag_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                    </a>

                                                @else

                                                    <a  class="btn btn-success btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.hashtags.status' , ['hashtag_id' => $hashtag->id] ) }}">&nbsp;{{tr('approve')}}</a> 
                                                @endif
                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">
                                                <a  class="btn btn-info btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.posts.index', ['hashtag_id' => $hashtag->id] ) }}">&nbsp;{{tr('total_posts')}}</a> 
                                            </div>

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

