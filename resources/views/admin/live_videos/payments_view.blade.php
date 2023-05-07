@extends('layouts.admin') 

@section('title', tr('payments')) 

@section('content-header', tr('payments')) 

@section('breadcrumb')


    
<li class="breadcrumb-item">
    <a href="{{route('admin.post.payments')}}">{{ tr('payments') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('live_video_payment') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('live_video_payment') }}</h4>
                    
                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        <div class="row">
                            
                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $live_video_payment->unique_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $live_video_payment->payment_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $live_video_payment->payment_mode}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('from_username')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $live_video_payment->live_video_viewer_id])}}">
                                                    {{ $live_video_payment->fromUser->name ?? tr('n_a')}}</td>
                                                </a>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('to_username')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $live_video_payment->user_id])}}">
                                                {{ $live_video_payment->user->name ?? tr('n_a')}}
                                                </a>
                                            </td>
                                        </tr> 

                                        <tr>
                                            <td>{{ tr('stream_title')}} </td>
                                            <td>
                                                <a href="{{ route('admin.live_videos.view', ['live_video_id' => $live_video_payment->live_video_id])}}">
                                                {{ $live_video_payment->videoDetails->title ?? "-"}}
                                                </a>
                                            </td>
                                        </tr> 

                                        <tr>
                                            <td>{{ tr('paid_token') }}</td>
                                            <td>{{formatted_amount($live_video_payment->token)}}</td>
                                        </tr>

                                        <!-- <tr>
                                            <td>{{ tr('paid_amount') }}</td>
                                            <td>{{ formatted_amount($live_video_payment->amount)}}</td>
                                        </tr> -->

                                        <tr>
                                            <td>{{ tr('admin_amount') }}</td>
                                            <td>{{ $live_video_payment->admin_amount_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('user_amount') }}</td>
                                            <td>{{ $live_video_payment->user_amount_formatted}}</td>
                                        </tr>
                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">
                                
                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($live_video_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status') }}</td>
                                            <td>
                                                @if($live_video_payment->status ==YES)

                                                    <span class="badge bg-success">{{tr('paid')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('pending')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('is_failed') }}</td>
                                            <td>
                                                @if($live_video_payment->is_failed ==YES)

                                                    <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        @if($live_video_payment->is_failed ==YES)
                                        <tr>
                                            <td>{{ tr('failed_reason') }}</td>
                                            <td>{{ $live_video_payment->failed_reason}}</td>
                                        </tr>
                                        @endif

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($live_video_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($live_video_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection