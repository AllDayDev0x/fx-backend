@extends('layouts.admin') 

@section('title', tr('payments')) 

@section('content-header', tr('payments')) 

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="">{{ tr('payments') }}</a>
</li>
<li class="breadcrumb-item">{{tr('order_payments')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('order_payments') }}</h4>

                    <div class="heading-elements">

                        <a href="{{ route('admin.order_payment.excel',['order_id'=>Request::get('order_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.csv']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to CSV</a>

                        <a href="{{ route('admin.order_payment.excel',['order_id'=>Request::get('order_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.xls']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLS</a>

                        <a href="{{ route('admin.order_payment.excel',['order_id'=>Request::get('order_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.xlsx']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLSX</a>
                        
                    </div>
                    
                </div>

                <div class="box box-outline-purple">
                
                    <div class="box-body">

                        @include('admin.orders._payment_search')

                        <div class="table-responsive">
                        
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                    
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('order_id')}}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('sub_total') }}</th>
                                    <th>{{ tr('delivery_price') }}</th>
                                    <th>{{ tr('total') }}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($order_payments as $i => $order_payment)

                                    <tr>
                                        <td>{{ $i + 1 }}</td>

                                        <td>
                                            <a href="{{route('admin.users.view',['user_id' => $order_payment->user_id])}}">{{$order_payment->user->name ?? tr('n_a')}}</a>
                                        </td>

                                        <td>{{$order_payment->userOrder->unique_id ?? tr('n_a')}}</td>

                                        <td>
                                            <a href="{{route('admin.orders.view',['order_id' => $order_payment->order_id])}}">
                                                {{ $order_payment->payment_id }}
                                            </a>
                                        </td>

                                        <td>{{$order_payment->sub_total_formatted}}</td>

                                        <td>
                                            {{ $order_payment->delivery_price_formatted}}
                                        </td>

                                        <td>{{$order_payment->total_formatted}}</td>

                                        <td>
                                            @if($order_payment->status == APPROVED)

                                                <span class="badge badge-success badge-lg">{{ tr('approved') }}</span> 
                                            @else

                                                <span class="badge badge-warning badge-lg">{{ tr('declined') }}</span> 
                                            @endif
                                        </td>

                                        <td><a class="btn btn-info" href="{{route('admin.order.payments.view',['order_payment_id' => $order_payment->id])}}">{{tr('view')}}</a></td>

                                    </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $order_payments->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                      </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection