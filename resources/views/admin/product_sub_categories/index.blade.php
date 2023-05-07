@extends('layouts.admin') 

@section('title', tr('view_product_sub_categories')) 

@section('content-header', tr('product_sub_categories')) 

@section('breadcrumb')



<li class="breadcrumb-item">
    <a href="{{route('admin.product_sub_categories.index')}}">{{tr('product_sub_categories')}}</a>
</li>

<li class="breadcrumb-item active">{{tr('view_product_sub_categories')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_product_sub_categories') }}

                    @if($product_category)
                    - 
                    <a href="{{route('admin.product_categories.view',['product_category_id'=>$product_category->id ?? ''])}}">{{$product_category->name ?: ''}}</a>

                    @endif

                    </h4>

                    <div class="heading-elements">
                        <a href="{{ route('admin.product_sub_categories.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_product_sub_category') }}</a>
                    </div>
                    
                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        @include('admin.product_sub_categories._search')

                        <div class="table-responsive">

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                <thead>
                                    <tr>
                                        <th>{{ tr('s_no') }}</th>
                                        <th>{{ tr('picture') }}</th>
                                        <th>{{ tr('product_sub_category_name') }}</th>
                                        @if(!$product_category)
                                            <th>{{ tr('product_category_name') }}</th>
                                        @endif
                                        <th>{{tr('total_products')}}</th>
                                        <th>{{ tr('status') }}</th>
                                        <th>{{ tr('action') }}</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($product_sub_categories as $i => $product_sub_category)
                                    <tr>
                                        <td>{{ $i+$product_sub_categories->firstItem() }}</td>

                                        <td><img src="{{$product_sub_category->picture ?: asset('product-placeholder.jpeg')}}" class="category-image"></td>

                                        <td>
                                            <a href="{{  route('admin.product_sub_categories.view' , ['product_sub_category_id' => $product_sub_category->id] )  }}">
                                                {{ $product_sub_category->name ?: tr('n_a')}}
                                            </a>
                                        </td>

                                        @if(!$product_category)

                                            <td>
                                                <a href="{{  route('admin.product_categories.view' , ['product_category_id' => $product_sub_category->product_category_id] )  }}">
                                                    {{ $product_sub_category->productCategory->name ?? tr('n_a')}}
                                                </a>
                                            </td>

                                        @endif

                                        <td>
                                            <a href="{{route('admin.user_products.index',['product_sub_category_id' => $product_sub_category->id])}}">{{$product_sub_category->userProducts->count() ?? 0}}
                                           </a>
                                        </td>

                                        <td>
                                            @if($product_sub_category->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                                            @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                            @endif
                                        </td>

                                        <td>

                                            <div class="btn-group" role="group">

                                                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                                <div class="dropdown-menu dropdown-sm-scroll" aria-labelledby="btnGroupDrop1">

                                                    <a class="dropdown-item" href="{{ route('admin.product_sub_categories.view', ['product_sub_category_id' => $product_sub_category->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                    @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                    @else

                                                    <a class="dropdown-item" href="{{ route('admin.product_sub_categories.edit', ['product_sub_category_id' => $product_sub_category->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('product_sub_category_delete_confirmation' , $product_sub_category->name) }}&quot;);" href="{{ route('admin.product_sub_categories.delete', ['product_sub_category_id' => $product_sub_category->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                    @endif

                                                    @if($product_sub_category->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.product_sub_categories.status' , ['product_sub_category_id' => $product_sub_category->id] )  }}" onclick="return confirm(&quot;{{ $product_sub_category->name }} - {{ tr('product_sub_category_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                    </a> 

                                                    @else

                                                    <a class="dropdown-item" href="{{ route('admin.product_sub_categories.status' , ['product_sub_category_id' => $product_sub_category->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                    @endif

                                                    <a class="dropdown-item" href="{{route('admin.user_products.index',['product_sub_category_id' => $product_sub_category->id])}}">&nbsp;{{ tr('total_products') }}</a> 
                                                </div>

                                            </div>

                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>

                            </table>

                            <div class="pull-right" id="paglink">{{ $product_sub_categories->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection