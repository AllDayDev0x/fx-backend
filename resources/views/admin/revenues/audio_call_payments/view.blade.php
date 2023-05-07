@extends('layouts.admin') 

@section('content-header', tr('audio_call_payments')) 

@section('breadcrumb')


    
<li class="breadcrumb-item">
    <a href="{{route('admin.audio_call_payments.index')}}">{{ tr('audio_call_payments') }}</a>
</li>

<li class="breadcrumb-item active">{{tr('view_audio_call_payment')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_audio_call_payment') }}</h4>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="row">
                            
                            <div class="col-md-6">

                                <table class="table table-bordered table-striped tab-content table-responsive-sm">
                       
                                    <tbody>

                                      

                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $audio_call_payment->payment_id ?: tr(n_a)}}</td>
                                        </tr>


                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $audio_call_payment->payment_mode ?: tr(n_a)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('audio_call_request_id')}} </td>
                                            <td>
                                                <a href="{{ route('admin.audio_call_requests.view', ['audio_call_request_id' => $audio_call_payment->audiocallrequest->id ?? 0])}}">
                                                {{ $audio_call_payment->Audiocallrequest->unique_id ?? tr('n_a')}}
                                                </a>
                                            </td>
                                        </tr> 


                                        <tr>
                                            <td>{{ tr('requested_from')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $audio_call_payment->user_id])}}">
                                                {{ $audio_call_payment->user->name ?? tr('n_a')}}
                                                </a>
                                            </td>
                                        </tr> 

                                        <tr>
                                            <td>{{ tr('requested_to')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $audio_call_payment->model_id])}}">
                                                {{ $audio_call_payment->model->name ?? tr('n_a')}}
                                                </a>
                                            </td>
                                        </tr> 

                                        <!-- <tr>
                                            <td>{{ tr('paid_token') }}</td>
                                            <td>{{$audio_call_payment->paid_amount_formatted ?: 0}}</td>
                                        </tr> -->

                                        <tr>
                                            <td>{{ tr('paid_amount') }}</td>
                                            <td>{{ $audio_call_payment->paid_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('admin_amount') }}</td>
                                            <td>{{ $audio_call_payment->admin_amount_formatted ?: 0}}</td>
                                        </tr>

                                      
                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">
                                
                                <table class="table table-bordered table-striped tab-content table-responsive-sm">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('user_amount') }}</td>
                                            <td>{{ $audio_call_payment->user_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($audio_call_payment->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status') }}</td>
                                            <td>
                                                @if($audio_call_payment->status ==PAID)

                                                    <span class="badge bg-success">{{tr('paid')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('not_paid')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('is_failed') }}</td>
                                            <td>
                                                @if($audio_call_payment->is_failed ==YES)

                                                    <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        @if($audio_call_payment->status != PAID)

                                        <tr>
                                            <td>{{ tr('failed_reason') }}</td>
                                            <td>{{ $audio_call_payment->failed_reason ?: tr('n_a')}}</td>
                                        </tr>

                                        @endif

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($audio_call_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($audio_call_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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