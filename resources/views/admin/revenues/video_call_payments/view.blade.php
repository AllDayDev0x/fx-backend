@extends('layouts.admin') 

@section('content-header', tr('video_call_payments')) 

@section('breadcrumb')


    
<li class="breadcrumb-item">
    <a href="{{route('admin.video_call_payments.index')}}">{{ tr('video_call_payments') }}</a>
</li>

<li class="breadcrumb-item active">{{tr('view_video_call_payment')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_video_call_payment') }}</h4>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="row">
                            
                            <div class="col-md-6">

                                <table class="table table-bordered table-striped tab-content table-responsive-sm">
                       
                                    <tbody>

                                      

                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $video_call_payment->payment_id ?: tr(n_a)}}</td>
                                        </tr>


                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $video_call_payment->payment_mode ?: tr(n_a)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('video_call_request_id')}} </td>
                                            <td>
                                                <a href="{{ route('admin.video_call_requests.view', ['video_call_request_id' => $video_call_payment->videocallrequest->id ?? 0])}}">
                                                {{ $video_call_payment->videocallrequest->unique_id ?? "-"}}
                                                </a>
                                            </td>
                                        </tr> 


                                        <tr>
                                            <td>{{ tr('requested_from')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $video_call_payment->user_id])}}">
                                                {{ $video_call_payment->user->name ?? "-"}}
                                                </a>
                                            </td>
                                        </tr> 

                                        <tr>
                                            <td>{{ tr('requested_to')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $video_call_payment->model_id])}}">
                                                {{ $video_call_payment->model->name ?? "-"}}
                                                </a>
                                            </td>
                                        </tr> 

                                        <tr>
                                            <td>{{ tr('paid_amount') }}</td>
                                            <td>{{$video_call_payment->paid_amount_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('admin_amount') }}</td>
                                            <td>{{ $video_call_payment->admin_amount_formatted}}</td>
                                        </tr>

                                      
                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">
                                
                                <table class="table table-bordered table-striped tab-content table-responsive-sm">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('user_amount') }}</td>
                                            <td>{{ $video_call_payment->user_amount_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($video_call_payment->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status') }}</td>
                                            <td>
                                                @if($video_call_payment->status ==PAID)

                                                    <span class="badge bg-success">{{tr('paid')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('not_paid')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('is_failed') }}</td>
                                            <td>
                                                @if($video_call_payment->is_failed ==YES)

                                                    <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        @if($video_call_payment->status != PAID)

                                        <tr>
                                            <td>{{ tr('failed_reason') }}</td>
                                            <td>{{ $video_call_payment->failed_reason ?? tr('n_a')}}</td>
                                        </tr>

                                        @endif

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($video_call_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($video_call_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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