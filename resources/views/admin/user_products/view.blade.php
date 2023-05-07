@extends('layouts.admin')

@section('title', tr('view_user_products'))

@section('content-header', tr('user_products'))

@section('breadcrumb')

    
    <li class="breadcrumb-item"><a href="{{route('admin.user_products.index')}}">{{tr('user_products')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_user_products')}}</a>
    </li>

@endsection

@section('content')
<section class="content">

<div class="content-body">

    <div id="user-profile">

        <div class="row">

            <div class="col-12">

                <div class="card profile-with-cover">

                    <div class="media profil-cover-details">
                        <div class="media-left pl-2 pt-2 col-lg-8 col-md-8 col-sm-12">
                            <a  class="profile-image">
                              <img src="{{ $user_product->picture ?: asset('placeholder.png')}}" class="img-thumbnail img-fluid img-border" style="max-width: 70em; height: 45em; object-fit: contain;">
                            </a>
                        </div>
                        <div class="media-body pt-3 px-2 col-lg-4 col-md-4 col-sm-12">
                            <div class="row">
                                <div class="col">
                                    <h3 class="card-title">{{ $user_product->name ?: tr('n_a') }}</h3>
                                </div>

                            </div>
                            <div class="row">

                                <div class="col-lg-6 col-md-12 col-sm-12">

                                    <a class="btn btn-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.user_products.edit', ['user_product_id'=>$user_product->id] )}}"> &nbsp;{{tr('edit')}}</a>

                                 </div>

                                <div class="col-lg-6 col-md-12 col-sm-12">

                                     @if($user_product->status == APPROVED)
                                     <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.user_products.status' ,['user_product_id'=> $user_product->id] )}}" onclick="return confirm(&quot;{{$user_product->name}} - {{tr('user_product_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                                    @else

                                    <a  class="btn btn-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.user_products.status' , ['user_product_id'=> $user_product->id] )}}">&nbsp;{{tr('approve')}}</a> 
                                    @endif

                                </div>

                                <div class="col-lg-6 col-md-12 col-sm-12">

                                    <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('user_product_delete_confirmation' , $user_product->name)}}&quot;);" href="{{route('admin.user_products.delete', ['user_product_id'=> $user_product->id] )}}">&nbsp;{{tr('delete')}}</a>

                                </div>  
                                <br><br>
                                <!-- <div class="col-lg-4">

                                    <a class="btn btn-info btn-block btn-min-width mr-1 mb-1" href="{{route('admin.product_inventories.index',['user_product_id' => $user_product->id])}}">&nbsp;{{tr('inventory')}}</a>

                                </div> -->

                                 <div class="col-lg-6 col-md-12 col-sm-12">

                                    <a class="btn btn-primary btn-block btn-min-width mr-1 mb-1" href="{{route('admin.order_products',['user_product_id' => $user_product->id])}}">&nbsp;{{tr('orders')}}</a>

                                </div>

                                 <div class="col-lg-6 col-md-12 col-sm-12">

                                    <a class="btn btn-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.user_products.dashboard',['user_product_id' => $user_product->id])}}">&nbsp;{{tr('dashboard')}}</a>

                                </div>   


                            </div>

                            <div class="row">

                               
                             </div>
                        
                        </div>

                        <nav class="navbar navbar-light navbar-profile align-self-end">
                       
                        </nav>
                    </div>
                </div>
  
            <div class="col-xl-12 col-lg-12">

                <div class="card">

                    <div class="card-header border-bottom border-gray">

                          <h4 class="card-title">{{tr('user_product')}}</h4>
                    </div>

                    <div class="box box-outline-purple">

                    <div class="box-body">

                        <div class="table-responsive">

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                <tr>
                                    <th>{{tr('product_name')}}</th>
                                    <td>{{$user_product->name ?: tr('n_a')}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('content_creator_name')}}</th>

                                    <td><a href="{{route('admin.users.view',['user_id' => $user_product->user_id])}}">{{$user_product->user->name ?? tr('n_a')}}</a></td>
                                </tr>

                                <tr>
                                    <th>{{tr('quantity')}}</th>
                                    <td>{{$user_product->quantity ?: 0}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('price')}}</th>
                                    <td>{{$user_product->user_product_price_formatted}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('product_category')}}</th>
                                    <td><a href="{{route('admin.product_categories.view',['product_category_id' => $user_product->product_category_id])}}">
                                        {{$user_product->productCategories->name ?? tr('n_a')}}</a>
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('product_sub_category')}}</th>
                                    <td><a href="{{route('admin.product_sub_categories.view',['product_sub_category_id' => $user_product->product_sub_category_id])}}">
                                        {{$user_product->productSubCategories->name ?? tr('n_a')}}</a>
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('available')}}</th>
                                    <td>
                                        @if($user_product->is_outofstock == OUT_OF_STOCK)

                                            <span class="badge badge-success">
                                                {{ tr('no') }}
                                            </span> 
                                        @else

                                            <span class="badge badge-danger">
                                                {{ tr('yes') }}
                                            </span> 
                                        @endif
                                    </td>

                                </tr>

                                <tr>
                                    <th>{{tr('status')}}</th>
                                    <td>
                                        @if($user_product->status == APPROVED) 

                                            <span class="badge badge-success">{{tr('approved')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('declined')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('description')}}</th>
                                    <td>{!!$user_product->description ?: tr('n_a')!!}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('created_at')}} </th>
                                  <td>{{common_date($user_product->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('updated_at')}} </th>
                                  <td>{{common_date($user_product->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>   
                                
                            </table>

                        </div>

                    </div>

                    </div>

                </div>

                <hr>

                @if(count($product_galleries)>0)
                
                    <div class="row">

                        <div class="col-lg-6 col-12">

                            <div class="box">
                                <div class="box-header with-border">
                                      <h3 class="box-title">{{tr('product_gallery')}}</h3>
                                </div>
                                <div class="box-body">
                                    <ul class="bo-slider">

                                        @foreach($product_galleries as $i => $product_gallery)

                                            <li data-url="{{ asset($product_gallery->picture)}}" data-type="image"></li>

                                        @endforeach
                                        
                                    </ul>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

</section> 
    
@endsection

@section('scripts')

    <script src="{{asset('admin-assets/bootstrap-slider/bootstrap-slider.js')}}" type="text/javascript"></script>

    <script src="{{asset('admin-assets/bootstrap-slider/script.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $('.bo-slider').boSlider({
            slideShow: false,
            interval: 3000,
            // animation: "fade"
        });

    </script>

@endsection
