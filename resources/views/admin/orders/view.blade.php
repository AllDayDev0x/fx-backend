@extends('layouts.admin') 

@section('title', tr('orders')) 

@section('content-header', tr('orders')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.orders.index')}}">{{ tr('orders') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_order') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_order') }} : {{$order->unique_id}}</h4>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="card-title text-primary"><h4><b>{{tr('shipping_details')}}</b>
                                </h4>

                                </div>

                                <table class="table table-bordered table-striped tab-content">
                       
                                    <tbody>
                                       
                                        <tr>
                                            <td>{{ tr('delivery_address_name')}} </td>
                                            <td>{{ $order->deliveryAddressDetails->name ?? tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('delivery_address')}}</td>
                                            <td>{{$order->deliveryAddressDetails->address ?? tr('n_a')}}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td>{{tr('pincode')}}</td>
                                            <td>{{$order->deliveryAddressDetails->pincode ?? tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('state')}}</td>
                                            <td>{{$order->deliveryAddressDetails->state ?? tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('landmark')}}</td>
                                            <td>{{$order->deliveryAddressDetails->landmark ?? tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('contact_number')}}</td>
                                            <td>{{$order->deliveryAddressDetails->contact_number ?? tr('n_a')}}</td>
                                        </tr>

                                        <!-- <tr>
                                            <td>{{ tr('order_id')}} </td>
                                            <td>{{ $order->unique_id ?: tr('n_a')}}</td>
                                        </tr> -->

                                    </tbody>

                                </table>
                                
                            </div>

                            <div class="col-md-6">

                                <div class="card-title text-primary"><h4><b>{{tr('order_details')}}</b></h4></div>

                                <table class="table table-bordered table-striped tab-content">
                       
                                    <tbody>
                                        
                                        <tr>
                                            <td>{{ tr('product_quantity')}} </td>
                                            <td>{{ $order->total_products  ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('sub_total') }}</td>
                                            <td>{{ $order->sub_total_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('tax_price') }}</td>
                                            <td>{{ $order->tax_price_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('delivery_price') }}</td>
                                            <td>{{ $order->delivery_price_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('total_token') }}</td>
                                            <td>{{$order->total_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('status')}}</td>
                                            <td>
                                                @switch($order->status)

                                                    @case(SORT_BY_ORDER_CANCELLED)
                                                        <span class="badge bg-danger">{{tr('cancelled')}}</span>
                                                        @break

                                                    @case(SORT_BY_ORDER_SHIPPED)
                                                        <span class="badge bg-secondary">{{tr('shipped')}}</span>
                                                        @break

                                                    @case(SORT_BY_ORDER_DELIVERD) 
                                                        <span class="badge bg-success">
                                                            {{tr('deliverd')}}
                                                        </span>
                                                        @break

                                                    @case(SORT_BY_ORDER_PACKED) 
                                                        <span class="badge bg-warning">
                                                            {{tr('packed')}}
                                                        </span>
                                                        @break

                                                    @default
                                                        <span class="badge bg-primary">{{tr('placed')}}</span>

                                                @endswitch
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('ordered_date')}} </td>
                                            <td>{{common_date($order->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <!-- <tr>
                                            <td>{{ tr('total_token') }}</td>
                                            <td>{{$order->token ?: 0}}</td>
                                        </tr> -->
                                        
                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card">

                    <div class="card-body"> 

                        <div class="card-title text-primary"><h4><b>{{tr('ordered_product_details')}}</b></h4>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <table class="table table-bordered">
                                
                                    <thead>
                                        <tr>
                                            <th>{{ tr('product_name') }}</th>
                                            <th>{{ tr('quantity')}}</th>
                                            <th>{{ tr('per_quantity_price') }}</th>
                                            <th>{{ tr('sub_total') }}</th>
                                            <th>{{ tr('tax_price')}}</th>
                                            <th>{{ tr('delivery_price') }}</th>
                                            <th>{{ tr('total_token') }}</th>
                                        </tr>
                                    </thead>
                                   
                                    <tbody>

                                        @foreach($order_products as $i => $order_product_details)

                                        <tr>

                                            <td>
                                                <a href="{{route('admin.user_products.view',['user_product_id' => $order_product_details->user_product_id])}}">{{ $order_product_details->userProductDetails->name ?? tr('n_a')}}</a>
                                            </td>

                                            <td>{{ $order_product_details->quantity ?: 0}}</td>

                                            <td>{{ $order_product_details->per_quantity_price_formatted ?: 0}}</td>

                                            <td>{{ $order_product_details->sub_total_formatted ?: 0}}</td>

                                            <td>{{ $order_product_details->tax_price_formatted ?: 0}}</td>

                                            <td>{{ $order_product_details->delivery_price_formatted ?: 0}}</td>

                                            <td>{{$order_product_details->total_formatted ?: 0}}</td>

                                        </tr>

                                        @endforeach

                                    </tbody>
                                
                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card">

                    <div class="card-body"> 

                        <div class="card-title text-primary"><h4>                       
                            <b>{{tr('order_payment_history')}}</b></h4>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <table class="table table-bordered">
                       
                                    <tbody>
                                       
                                        <tr>
                                            <th>{{ tr('order_id')}} </th>
                                            <td>{{$order->unique_id ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('payment_id')}}</th>
                                            <td> {{ $order_payment->payment_id ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('payment_mode')}}</th>
                                            <td> {{ $order_payment->payment_mode ?: tr('n_a')}}</td>
                                        </tr>
                                        
                                        <tr>
                                            <th>{{tr('user')}}</th>
                                            <td><a href="{{route('admin.users.view',['user_id' => $order_payment->user_id ?? 0])}}">{{ $order_payment->user->name ?? tr('n_a') }}</a></td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('sub_total')}}</th>
                                            <td>{{$order_payment->sub_total_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('delivery_price')}}</th>
                                            <td>{{ $order_payment->delivery_price_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('tax_price')}}</th>
                                            <td>{{$order_payment->tax_price_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('total_token')}}</th>
                                            <td>{{$order_payment->total_formatted ?: 0}}</td>
                                        </tr>

                                        <!-- <tr>
                                            <th>{{tr('total_token')}}</th>
                                            <td>{{$order_payment->token ?: 0}}</td>
                                        </tr> -->

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

