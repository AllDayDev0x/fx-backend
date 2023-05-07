<section class="content">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$user_product->id ? tr('edit_user_product') : tr('add_user_product')}}</h4>

                    <div class="heading-elements">
                        <a href="{{route('admin.user_products.index') }}" class="btn btn-primary"><i class="ft-user icon-left"></i>{{ tr('view_user_products') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.user_products.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="user_product_id" id="user_product_id" value="{{ $user_product->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('product_name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $user_product->name ?: old('name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quantity">{{tr('quantity')}}*</label>
                                            <input type="number" min="1" pattern="[0-9]{6,13}" id="quantity" name="quantity" class="form-control" placeholder="{{tr('quantity')}}" value="{{ $user_product->quantity ?: old('quantity') }}" required>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">{{tr('token')}}*</label>
                                            <input type="number" step=".01" min="1" pattern="[0-9]{6,13}" id="price" name="price" class="form-control" placeholder="{{tr('token')}}" value="{{(Setting::get('is_only_wallet_payment') ? $user_product->token : $user_product->price) ?: old('price') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">{{ tr('select_content_creator') }} <span class="admin-required">*</span> </label>
                                            <select class="form-control select2" id="user_id" name="user_id" required>
                                            <option value="">{{tr('select_content_creator')}}</option>
                                            @foreach($users as $user_details)
                                                <option class="select-color" value="{{$user_details->id}}"@if($user_details->is_selected == YES) selected @endif >
                                                    {{$user_details->name}}
                                                </option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                       <div class="form-group">

                                        <label for="category">{{tr('choose_category')}} *</label>

                                        <select class="form-control select2" id="product_category_id" name="product_category_id" required>

                                            <option value="">{{tr('choose_category')}}</option>

                                            @foreach($product_categories as $category_details)
                                                <option value="{{$category_details->id}}" @if($category_details->is_selected == YES) selected @endif>{{$category_details->name}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">

                                       <label for="product_sub_category_id">{{tr('choose_product_sub_category')}} *</label>

                                        <select class="form-control select2" id="product_sub_category_id" name="product_sub_category_id" required>

                                            <option value="">{{tr('choose_product_sub_category')}}</option>

                                            @foreach($product_sub_categories as $i => $sub_category_details)
                                                <option value="{{$sub_category_details->id}}" @if($sub_category_details->is_selected == YES) selected @endif>{{$sub_category_details->name}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    </div>

                                </div>

                                <div class="row">

                                
                                    <div class="col-md-12">

                                        <div class="form-group">

                                            <label>{{ tr('select_picture') }}</label>

                                            <input type="file" class="form-control" name="picture" accept="image/png,image/jpeg" >
                                            <p class="text-muted">{{tr('image_validate')}}</p>
                                                                      
                                        </div>
                                        
                                    </div>

                                </div>
                                
                            </div>

                            <div class="row">

                                <div class="col-md-12"> 

                                    <div class="form-group">

                                        <label for="description">{{tr('description')}}</label>

                                        <textarea rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}">{{old('description') ?: $user_product->description}}</textarea>

                                    </div>

                                </div>

                            </div>
                          
                            <div class="form-actions">

                                <div class="pull-right">
                                
                                    <button type="reset" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i> {{ tr('reset') }} 
                                    </button>

                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                                
                                </div>

                                <div class="clearfix"></div>

                            </div>

                        </form>
                        
                    </div>
                
                </div>

            </div>
        
        </div>
    
    </div>

</section>

