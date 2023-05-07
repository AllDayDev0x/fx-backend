@extends('layouts.admin')

@section('title', tr('live_videos'))

@section('content-header', tr('live_videos'))

@section('breadcrumb')



<li class="breadcrumb-item">
    <a href="{{route('admin.live_videos.index')}}">{{ tr('live_videos') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_live_videos') }}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_live_videos') }} - <!-- <a
                    href="{{route('admin.users.view',['user_id' => $live_video->user_id])}}"> -->{{$live_video->title ?? tr('n_a')}}<!-- </a> --> </h4>

                    <div class="heading-elements">

                        <a href="{{route('admin.live_videos.payments' , ['live_video_id' =>$live_video->id])}}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('payments') }}</a>

                    </div>

                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>

                                        <tr>
                                            <td>{{ tr('stream_id')}} </td>
                                            <td class="text-uppercase">{{ $live_video->unique_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('stream_title')}} </td>
                                            <td>{{ $live_video->title ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id' => $live_video->user_id])}}">
                                                    {{ $live_video->user->name ?? tr('n_a')}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status')}} </td>
                                            <td>
                                                {{$live_video->status_formatted}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('stream_type')}} </td>
                                            <td>
                                                {{ucfirst($live_video->type) ?: tr('n_a')}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_type') }}</td>
                                            <td>
                                                @if($live_video->payment_status)
                                                <label class="label label-warning">{{tr('paid')}}</label>
                                                @else
                                                <label class="label label-success">{{tr('free')}}</label>
                                                @endif
                                            </td>
                                        </tr>

                                        

                                        <tr>
                                            <td>{{ tr('streamed_at') }}</td>
                                            <td>{{common_date($live_video->created_at, Auth::guard('admin')->user()->timezone)}}</td>

                                        </tr>
                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>

                                        <tr>
                                            <td>{{ tr('viewer_count') }}</td>
                                            <td>{{$live_video->viewer_cnt}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('start_time') }}</td>
                                            <td>{{common_date($live_video->start_time,'','g:i A')}}</td>

                                        </tr>

                                        <tr>
                                            <td>{{ tr('end_time') }}</td>
                                            <td>{{common_date($live_video->end_time, '','g:i A')}}</td>

                                        </tr>

                                        <tr>
                                            <td>{{ tr('no_of_minutes') }}</td>
                                            <td>{{ $live_video->no_of_minutes}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($live_video->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($live_video->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>


                                        <tr>
                                            <td>{{ tr('description') }}</td>
                                            <td>{!! $live_video->description ?: tr('n_a') !!}</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                            @if($live_video->amount > 0)
                            <div class="col-md-6">
                                <h3>Revenue Details</h3>
                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>

                                        <!-- <tr>
                                            <td>{{ tr('video_token')}} </td>
                                            <td>{{$live_video->token}}</td>
                                        </tr> -->

                                        <tr>
                                            <td>{{ tr('video_amount')}} </td>
                                            <td>{{ ($live_video->amount_formatted)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('total_amount')}} </td>
                                            <td>{{ $live_video->live_video_amount}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('user_commission')}} </td>
                                            <td>{{ $live_video->user_amount}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('admin_commission') }}</td>
                                            <td>{{ $live_video->admin_amount}}</td>

                                        </tr>
                                    </tbody>

                                </table>

                            </div>
                            @endif
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection