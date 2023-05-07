@extends('layouts.admin') 

@section('title', tr('payments')) 

@section('content-header', tr('order_payments')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.order.payments')}}">{{ tr('payments') }}</a>
</li>

<li class="breadcrumb-item active">
    <a href="{{route('admin.order.payments')}}">
        {{ tr('order_payments') }}
    </a>
</li>

<li class="breadcrumb-item active">{{ tr('view_order_payment') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_order_payment') }}</h4>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="row">
                            
                            <div class="col-md-6">

                                <table class="table table-bordered table-striped tab-content">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('user')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $order_payment->user_id])}}">
                                                {{ $order_payment->user->name ?? "-"}}
                                                </a>
                                            </td>
                                        </tr> 

                                        <tr>
                                            <td>{{ tr('order_id')}} </td>
                                            <td>{{ $order_payment->userOrder->unique_id ?? tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $order_payment->payment_id ?: tr('n_a')}}</td>
                                        </tr> 

                                        <tr>
                                            <td>{{ tr('sub_total') }}</td>
                                            <td>{{ $order_payment->sub_total_formatted ?: 0}}</td>
                                        </tr>

                                         <tr>
                                            <td>{{tr('delivery_price')}}</td>
                                            <td>{{$order_payment->delivery_price_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('tax_price')}}</td>
                                            <td>{{$order_payment->tax_price_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('total')}} </td>
                                            <td>{{ $order_payment->total_formatted ?: 0}}</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">
                                
                                 <table class="table table-bordered table-striped tab-content">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('total_products')}} </td>

                                            <td>
                                                <a href="{{route('admin.orders.view',['order_id' => $order_payment->order_id])}}">
                                                    {{ $order_payment->userOrder->total_products ?? 0}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $order_payment->payment_mode ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($order_payment->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status') }}</td>
                                            <td>
                                                @if($order_payment->status ==YES)

                                                    <span class="badge bg-success">{{tr('paid')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('pending')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        @if($order_payment->is_failed ==YES)
                                        <tr>
                                            <td>{{ tr('is_failed') }}</td>
                                            <td>
                                                <span class="badge bg-success">{{tr('yes')}}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('failed_reason') }}</td>
                                            <td>{{ $order_payment->failed_reason ?: tr('n_a')}}</td>
                                        </tr>

                                        @else

                                        <tr>
                                            <td>{{ tr('is_failed') }}</td>
                                            <td>
                                                <span class="badge bg-danger">{{tr('no')}}</span>
                                            </td>
                                        </tr>

                                        @endif

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($order_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($order_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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