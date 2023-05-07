@extends('layouts.admin') 

@section('content-header', tr('product_categories')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.product_categories.index')}}">{{tr('product_categories')}}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_product_categories') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_product_categories') }}</h4>

                    <div class="heading-elements">
                        <a href="{{ route('admin.product_categories.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_category') }}</a>
                    </div>
                    
                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        @include('admin.product_categories._search')
                        
                        <div class="table-responsive">

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                                <thead>
                                    <tr>
                                        <th>{{ tr('s_no') }}</th>
                                        <th>{{ tr('picture') }}</th>
                                        <th>{{ tr('category_name') }}</th>
                                        <th>{{tr('product_sub_category_count')}}</th>
                                        <th>{{tr('total_products')}}</th>
                                        <th>{{ tr('status') }}</th>
                                        <th>{{ tr('action') }}</th>
                                    </tr>
                                </thead>
                               
                                <tbody>

                                    @foreach($product_categories as $i => $category)
                                    <tr>
                                        <td>{{ $i+$product_categories->firstItem() }}</td>

                                        <td><img src="{{$category->picture ?: asset('placeholder.jpg')}}" class="category-image"></td>

                                        <td>
                                            <a href="{{  route('admin.product_categories.view' , ['product_category_id' => $category->id] )  }}">
                                            {{ $category->name ?: tr('n_a')}}
                                            </a>
                                        </td>

                                        <td>
                                            <a href="{{route('admin.product_sub_categories.index',['product_category_id' => $category->id])}}">{{$category->productSubCategories->count() ?? 0}}
                                           </a>
                                        </td>

                                        <td>
                                            <a href="{{route('admin.user_products.index',['product_category_id' => $category->id])}}">{{$category->userProducts->count() ?? 0}}
                                           </a>
                                        </td>
                                        
                                        <td>
                                            @if($category->status == APPROVED)

                                                <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                                            @else

                                                <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                            @endif
                                        </td>

                                        <td>
                                        
                                            <div class="btn-group" role="group">

                                                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                                <div class="dropdown-menu dropdown-sm-scroll" aria-labelledby="btnGroupDrop1">

                                                    <a class="dropdown-item" href="{{ route('admin.product_categories.view', ['product_category_id' => $category->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                    @if(Setting::get('is_demo_control_enabled') == YES)

                                                        <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                        <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                    @else

                                                        <a class="dropdown-item" href="{{ route('admin.product_categories.edit', ['product_category_id' => $category->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                        <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('category_delete_confirmation' , $category->name) }}&quot;);" href="{{ route('admin.product_categories.delete', ['product_category_id' => $category->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                    @endif

                                                    @if($category->status == APPROVED)

                                                        <a class="dropdown-item" href="{{  route('admin.product_categories.status' , ['product_category_id' => $category->id] )  }}" onclick="return confirm(&quot;{{ $category->name }} - {{ tr('category_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                    </a> 

                                                    @else

                                                        <a class="dropdown-item" href="{{ route('admin.product_categories.status' , ['product_category_id' => $category->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                    @endif
                                                    <a class="dropdown-item" href="{{route('admin.user_products.index',['product_category_id' => $category->id])}}">&nbsp;{{ tr('total_products') }}</a> 

                                                    <a class="dropdown-item" href="{{route('admin.product_sub_categories.index',['product_category_id' => $category->id])}}">&nbsp;{{ tr('product_sub_categories') }}
                                                    </a>

                                                </div>

                                            </div>

                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>
                        
                            </table>

                            <div class="pull-right" id="paglink">{{ $product_categories->appends(request()->input())->links('pagination::bootstrap-4') }}</div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
