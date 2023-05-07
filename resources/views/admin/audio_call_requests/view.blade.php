@extends('layouts.admin')

@section('content-header', tr('audio_call_requests'))

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.audio_call_requests.index')}}">{{ tr('audio_call_requests') }}</a>
</li>

<li class="breadcrumb-item active">{{tr('view_requests')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_requests') }} - {{$audio_call_request->unique_id ?? tr('n_a')}}</h4>

                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>



                                        <tr>
                                            <td>{{ tr('user')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view' , ['user_id' => $audio_call_request->user_id])}}" class="custom-a">
                                                    {{$audio_call_request->user->name ?? tr('not_available')}}
                                                </a>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>{{ tr('model')}} </td>
                                            <td> 
                                                <a href="{{route('admin.users.view' , ['user_id' => $audio_call_request->model_id])}}" class="custom-a">
                                                    {{$audio_call_request->model->name ?? tr('not_available')}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('scheduled_date')}} </td>
                                            <td>
                                                {{common_date($audio_call_request->start_time , Auth::guard('admin')->user()->timezone)}}
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>{{ tr('end_time')}} </td>
                                            <td>
                                                @if($audio_call_request->call_status == AUDIO_CALL_REQUEST_ENDED)
                                                {{common_date($audio_call_request->end_time , Auth::guard('admin')->user()->timezone)}}
                                                @else
                                                {{"-"}}
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status')}} </td>
                                            <td>
                                                <span class="btn btn-success btn-sm">
                                                    {{call_status_formatted($audio_call_request->call_status,NO, $audio_call_request->payment_status)}}
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_status')}} </td>
                                            <td>
                                                @if($audio_call_request->payment_status == PAID)
                                                <span class="btn btn-success btn-sm">
                                                    {{tr('paid')}}
                                                </span>
                                                @else
                                                <span class="btn btn-danger btn-sm">
                                                    {{tr('not_paid')}}
                                                </span>
                                                @endif
                                            </td>
                                        </tr>



                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>

                                        @if($audio_call_payment)

                                         <tr>
                                            <td>{{ tr('payment_id') }}</td>
                                            <td>{{$audio_call_payment->payment_id ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_mode') }}</td>
                                            <td>{{$audio_call_payment->payment_mode ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('total') }}</td>
                                            <td>{{$audio_call_payment->paid_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('admin_amount') }}</td>
                                            <td>{{$audio_call_payment->admin_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('user_amount') }}</td>
                                            <td>{{$audio_call_payment->user_amount_formatted ?: 0}}</td>
                                        </tr>

                                        @endif


                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($audio_call_request->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($audio_call_request->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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