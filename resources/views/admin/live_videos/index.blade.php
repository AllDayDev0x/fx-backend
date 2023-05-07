@extends('layouts.admin')

@section('title', tr('live_videos'))

@section('content-header', tr('live_videos'))

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.live_videos.index')}}">{{ tr('live_videos') }}</a>
</li>

<li class="breadcrumb-item active">
    {{tr('live_videos')}}
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card view-post-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{$is_streaming ? tr('on_live') : tr('history')}}
                    </h4>

                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        @if($is_streaming == YES)
                            <form action="{{route('admin.live_videos.onlive') }}"method="GET" role="search">
                        @else
                            <form action="{{route('admin.live_videos.index') }}" method="GET" role="search">
                        @endif

                            <div class="row">

                                <!-- <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
                                    @if(Request::has('search_key'))
                                        <p class="text-muted">{{tr('search_results_for')}}<b>{{Request::get('search_key')}}</b></p>
                                    @endif
                                </div> -->

                                <div class="col-xs-12 col-sm-12 col-lg-3 col-md-3 md-full-width resp-mrg-btm-md">
                                  
                                    <select class="form-control select2" name="payment_status">

                                        <option  class="select-color" value="">{{tr('payment_type')}}</option>

                                        <option value="{{FREE_VIDEO}}" @if(Request::get('payment_status')==FREE_VIDEO && Request::get('payment_status')!='' ) selected @endif>{{tr('free_videos')}}</option>
                                        <option value="{{PAID_VIDEO}}" @if(Request::get('payment_status')==PAID_VIDEO ) selected @endif>{{tr('paid_videos')}}</option>

                                    </select>

                                </div>

                                <div class="col-xs-12 col-sm-12 col-lg-3 col-md-3 md-full-width resp-mrg-btm-md">
                                 
                                    <select class="form-control select2" name="video_type">

                                        <option  class="select-color" value="">{{tr('select_stream_type')}}</option>

                                        <option value="{{TYPE_PUBLIC}}" @if(Request::get('video_type')==TYPE_PUBLIC) selected @endif>{{tr('public_videos')}}</option>
                                        <option value="{{TYPE_PRIVATE}}" @if(Request::get('video_type')==TYPE_PRIVATE) selected @endif>{{tr('private_videos')}}</option>

                                    </select>

                                </div>

                                <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

                                    <div class="input-group">
                                       
                                        <input type="text" class="form-control" value="{{Request::get('search_key')??''}}" name="search_key"
                                        placeholder="{{tr('live_search_placeholder')}}"> <span class="input-group-btn">
                                        &nbsp

                                        <button type="submit" class="btn btn-default reset-btn">
                                          <i class="fa fa-search" aria-hidden="true"></i>
                                        </button>
                                        
                                        @if($is_streaming == YES)
                                            <a class="btn btn-default reset-btn" href="{{route('admin.live_videos.onlive')}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                                            </a>

                                        @else
                                            <a class="btn btn-default reset-btn" href="{{route('admin.live_videos.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                                            </a>

                                        @endif
                                           
                                        </span>

                                    </div>
                                    
                                </div>

                            </div>

                        </form>
                        <br>
                        @if($is_streaming == YES)
                        <div class="box-body">
                            <div class="callout bg-pale-secondary">
                                <h4>{{tr('notes')}}</h4>
                                <p>
                                    </p><ul>
                                        <li>
                                            {{tr('live_videos_notes')}}
                                        </li>
                                    </ul>
                                <p></p>
                            </div>
                        </div>
                        @else
                        <div class="box-body">
                            <div class="callout bg-pale-secondary">
                                <h4>{{tr('notes')}}</h4>
                                <p>
                                    </p><ul>
                                        <li>
                                            {{tr('live_videos__history_notes')}}
                                        </li>
                                    </ul>
                                <p></p>
                            </div>
                        </div>
                        @endif
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            <thead>
                                <tr>

                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('stream_title') }}</th>
                                    <th>{{ tr('user_name') }}</th>
                                    <th>{{ tr('stream_type') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <!-- <th>{{ tr('token') }}</th> -->
                                    <th>{{ tr('amount') }}</th>
                                    <th>{{ tr('payment_type') }}</th>
                                    <th>{{ tr('streamed_at') }}</th>
                                    <th>&nbsp;&nbsp;{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($live_videos as $i => $live_video)
                                <tr>

                                    <td>{{ $i+$live_videos->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.live_videos.view' , ['live_video_id' => $live_video->id] )  }}">
                                            {{ $live_video->title ?? tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $live_video->user_id] )  }}">
                                            {{ $live_video->user->name ?? tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>{{ ucfirst($live_video->type) ?: tr('n_a')}}</td>

                                    <td>
                                        {{ $live_video->status_formatted ?: tr('n_a')}}
                                    </td>

                                    <!-- <td>
                                        {{$live_video->token ?: 0}}
                                    </td> -->

                                    <td>
                                        {{ $live_video->amount_formatted ?: 0}}
                                    </td>

                                    <td>
                                        @if($live_video->payment_status)
                                        <label class="label label-warning">{{tr('paid')}}</label>
                                        @else
                                        <label class="label label-success">{{tr('free')}}</label>
                                        @endif
                                    </td>

                                  

                                    <td>{{common_date($live_video->created_at, Auth::guard('admin')->user()->timezone)}}</td>

                                    <td>
                                        
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>


                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{route('admin.live_videos.view' , ['live_video_id' =>$live_video->id])}}">&nbsp;{{ tr('view') }}</a>


                                                @if($live_video->payment_status == PAID)

                                                <a class="dropdown-item" href="{{route('admin.live_videos.payments' , ['live_video_id' =>$live_video->id])}}">&nbsp;{{ tr('payments') }}</a>

                                                @endif

                                            </div>

                                        </div>
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $live_videos->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection