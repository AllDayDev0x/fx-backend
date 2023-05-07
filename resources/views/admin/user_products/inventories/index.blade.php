@extends('layouts.admin') 

@section('title', tr('product_inventories')) 

@section('content-header', tr('product_inventories')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.product_inventories.index')}}">{{ tr('product_inventories') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_product_inventories') }}
</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">
                        {{$title}}
                    </h4>
                   
                    
                </div>

                 <div class="box box-outline-purple">

                    <div class="box-body">

                        @include('admin.user_products.inventories._search')

                        <div class="table-responsive">

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('product_name') }}</th>
                                    <th>{{ tr('total_quantity') }}</th>
                                    <th>{{ tr('used_quantity') }}</th>
                                    <th>{{ tr('remaining_quatity') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($product_inventories as $i => $product_inventory)
                                <tr>
                                    <td>{{ $i+$product_inventories->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.user_products.view' , ['user_product_id' => $product_inventory->user_product_id] )  }}">
                                        {{ $product_inventory->userProductDetails->name ?? tr('n_a') }}
                                        </a>
                                    </td>
                                    
                                    <td>
                                        {{ $product_inventory->total_quantity ?: 0}}
                                    </td>

                                     <td>
                                        {{ $product_inventory->used_quantity ?: 0}}
                                    </td>

                                     <td>
                                        {{ $product_inventory->remaining_quantity ?: 0}}
                                    </td>


                                    <td>
                                        <a class="btn btn-primary" href="{{ route('admin.product_inventories.view', ['product_inventory_id' => $product_inventory->id] ) }}">&nbsp;{{ tr('view') }}</a> 
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $product_inventories->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                     </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection