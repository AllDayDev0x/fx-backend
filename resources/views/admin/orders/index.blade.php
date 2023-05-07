@extends('layouts.admin') 

@section('title', tr('orders')) 

@section('content-header', tr('orders')) 

@section('breadcrumb')


    
<li class="breadcrumb-item active">
    <a href="">{{ tr('orders') }}</a>
</li>

<li class="breadcrumb-item">
    {{Request::get('new_orders') ? tr('new_orders') : tr('view_orders')}}
</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    @if(Request::get('new_orders'))

                        <h4 class="card-title">{{ tr('new_orders') }}

                            @if($user)
                                - 
                                <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? tr('n_a')}}</a>

                            @endif

                        </h4>

                    @else

                        <h4 class="card-title">{{ tr('view_orders') }}

                            @if($user)
                                - 
                                <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? tr('n_a')}}</a>

                            @endif

                        </h4>

                    @endif
                    
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        @include('admin.orders._search')
                        
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <!-- <th>{{ tr('username') }}</th> -->
                                    <th>{{ tr('delivery_address')}}</th>
                                    <th>{{ tr('order_id')}}</th>
                                    <th>{{ tr('total_products') }}</th>
                                    <th>{{ tr('token')}}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($orders as $i => $order)

                                <tr>
                                    <td>{{ $i + $orders->firstItem() }}</td>

                                    <!-- <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $order->user_id] )  }}">
                                        {{ $order->user->name ?? tr('n_a') }}
                                        </a>
                                    </td> -->

                                    <td>
                                        <a href="{{  route('admin.delivery_address.view' , ['delivery_address_id' => $order->delivery_address_id] )  }}">
                                        {{ $order->deliveryAddressDetails->name ?? tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>
                                       <a href="{{  route('admin.orders.view' , ['order_id' => $order->id] )  }}">
                                        {{$order->unique_id ?: tr(n_a)}}
                                       </a>
                                    </td>

                                    <td>
                                        {{ $order->total_products ?: 0}}
                                    </td>

                                    <td>{{formatted_amount($order->total)}}</td>

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
                                                <span class="badge bg-info">
                                                    {{tr('packed')}}
                                                </span>
                                                @break
                                                
                                            @default
                                                <span class="badge bg-primary">{{tr('placed')}}</span>

                                        @endswitch
                                    </td>

                                    <td>
                                    
                                        <a class="btn btn-success" href="{{route('admin.orders.view',['order_id' => $order->id])}}">{{tr('view')}}</a>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $orders->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection