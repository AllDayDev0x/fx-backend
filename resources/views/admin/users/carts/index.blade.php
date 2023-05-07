@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('carts'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{tr('carts')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">
        <div class="col-12">
        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">

                    @if($user)
                    
                    <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a>

                    @endif

                </h4>

            </div>

            <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('product_name') }}</th>
                                    <th>{{ tr('quantity') }}</th>
                                    <th>{{ tr('price') }}</th>
                                    <th>{{ tr('tax_price') }}</th>
                                    <th>{{ tr('delivery_price') }}</th>
                                    <th>{{ tr('total') }}</th>
                                    <th>{{ tr('added_date')}}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($carts as $i => $cart)

                                <tr>

                                    <td>{{ $i+$carts->firstItem() }}</td>

                                    <td>
                                        <a href="{{ route('admin.user_products.view', ['user_product_id' => $cart->user_product_id] ) }}" class="custom-a">
                                            {{$cart->user_product->name ?? tr('n_a')}}
                                        </a>
                                    </td>

                                    <td>{{$cart->quantity ?: 0}}</td>

                                    <td>{{formatted_amount($cart->per_quantity_price ?: 0)}}</td>

                                    <td>{{formatted_amount($cart->tax_price ?: 0)}}</td>

                                    <td>{{formatted_amount($cart->delivery_price ?: 0)}}</td>

                                    <td>{{formatted_amount($cart->total ?: 0)}}</td>

                                    <td>{{common_date($cart->created_at,Auth::guard('admin')->user()->timezone) }}</td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.user_products.view', ['user_product_id' => $cart->user_product_id] ) }}">&nbsp;{{ tr('view') }}</a>

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{tr('user_cart_remove_confirmation')}}&quot;);" href="{{ route('admin.users.carts.remove', ['cart_id' => $cart->id] ) }}">&nbsp;{{ tr('remove') }}</a>
                                                

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $carts->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /card -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->

@endsection
