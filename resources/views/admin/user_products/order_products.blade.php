@extends('layouts.admin') 

@section('title', tr('products')) 

@section('content-header', tr('products')) 

@section('breadcrumb')

<li class="breadcrumb-item active"><a href="">{{ tr('products') }}</a></li>

<li class="breadcrumb-item">{{tr('view_orders')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ $title }}  - <a
                    href="{{route('admin.user_products.view',['user_product_id' => $product->id])}}">{{$product->name ?? tr('n_a')}}</a></h4>
                    
                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        @include('admin.user_products._search_order_products')

                        <div class="table-responsive">

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                
                            <thead>
                                <tr>
                                    <!-- <th>{{ tr('product_name') }}</th> -->
                                    <th>{{tr('s_no')}}
                                    <th>{{ tr('order_id') }}</th>
                                    <th>{{ tr('quantity')}}</th>
                                    <th>{{ tr('per_quantity_price') }}</th>
                                    <th>{{ tr('sub_total') }}</th>
                                    <th>{{ tr('tax_price')}}</th>
                                    <th>{{ tr('delivery_price') }}</th>
                                    <th>{{ tr('total') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($order_products as $i => $order_product)

                                <tr>

                                    <!-- <td>
                                        <a href="{{route('admin.user_products.view',['user_product_id' => $order_product->user_product_id])}}">{{ $order_product->userProductDetails->name ?? tr('n_a')}}</a>
                                    </td> -->

                                    <td>{{ $i + $order_products->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.orders.view',['order_id' => $order_product->order_id])}}">
                                            {{ $order_product->userOrder->unique_id ?? tr('n_a')}}
                                        </a>
                                    </td>

                                    <td>{{ $order_product->quantity}}</td>

                                    <td>{{ $order_product->per_quantity_price_formatted ?: 0}}</td>

                                    <td>{{ $order_product->sub_total_formatted ?: 0}}</td>

                                    <td>{{ $order_product->tax_price_formatted ?: 0}}</td>

                                    <td>{{ $order_product->delivery_price_formatted ?: 0}}</td>

                                    <td>{{$order_product->total_formatted ?: 0}}</td>

                                    <td>

                                        <a class="btn btn-info" href="{{route('admin.orders.view',['order_id' => $order_product->order_id])}}">&nbsp;{{ tr('view') }}</a> 

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                            </table>

                            <div class="pull-right" id="paglink">{{ $order_products->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection