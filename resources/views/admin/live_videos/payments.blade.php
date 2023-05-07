@extends('layouts.admin') 

@section('title', tr('revenue_management')) 

@section('content-header', tr('payments')) 

@section('breadcrumb')
    
<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>

<li class="breadcrumb-item active">{{ tr('live_video_payments') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row ">

        <div class="col-12 ">

            <div class="card post-payment-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ $title }} 
                        
                    @if($user)
                    - 
                    <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a>
                    @endif
                    
                   </h4>

                   <div class="heading-elements">

                        <a href="{{ route('admin.live_video_payment.excel',['live_video_id'=>Request::get('live_video_id'),'search_key'=>Request::get('search_key'),'user_id'=>Request::get('user_id'),'file_format'=>'.csv']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to CSV</a>

                        <a href="{{ route('admin.live_video_payment.excel',['live_video_id'=>Request::get('live_video_id'),'search_key'=>Request::get('search_key'),'user_id'=>Request::get('user_id'),'file_format'=>'.xls']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLS</a>

                        <a href="{{ route('admin.live_video_payment.excel',['live_video_id'=>Request::get('live_video_id'),'search_key'=>Request::get('search_key'),'user_id'=>Request::get('user_id'),'file_format'=>'.xlsx']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLSX</a>
                        
                    </div>
                    
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        @include('admin.live_videos._payment_search')
                        
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('live_video')}}</th>
                                    <th >{{ tr('payment_id') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('admin_amount') }}</th>
                                    <th>{{ tr('user_amount') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($live_video_payments as $i => $live_video_payment)
                                <tr>

                                    <td>{{ $i+$live_video_payments->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $live_video_payment->fromUser->id ?? 0] )  }}">
                                        {{ $live_video_payment->fromUser->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.live_videos.view' , ['live_video_id' => $live_video_payment->live_video_id] )  }}">
                                        {{$live_video_payment->videoDetails->unique_id ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ $live_video_payment->payment_id }}
                                        <br>
                                        <br>
                                        <span class="text-gray">{{tr('date')}}: {{common_date($live_video_payment->created_at, Auth::user()->timezone)}}</span>
                                    </td>
                                    
                                    <td>
                                        {{formatted_amount($live_video_payment->token)}}
                                    </td>

                                    <td>
                                        {{ $live_video_payment->admin_amount_formatted}}
                                    </td>

                                    <td>
                                        {{ $live_video_payment->user_amount_formatted}}
                                    </td>


                                    <td>
                                        <span class="badge badge-secondary">
                                        {{ $live_video_payment->payment_mode}}
                                        </span>
                                    </td>
                                        
                                    <td>
                                        
                                        <a href="{{route('admin.live_videos.payments.view',['live_video_payment_id' => $live_video_payment->id])}}" class="btn btn-primary">{{tr('view')}}</a>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $live_video_payments->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection

@section('styles')
<style>
    .table th, .table td {
    padding: 0.75rem 1.5rem !important;
}
</style>
@endsection