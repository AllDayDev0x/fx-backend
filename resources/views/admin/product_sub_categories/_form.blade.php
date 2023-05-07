<section class="content">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$product_sub_category->id ? tr('edit_product_sub_category') : tr('add_product_sub_category')}}</h4>

                    <div class="heading-elements">
                        <a href="{{route('admin.product_sub_categories.index') }}" class="btn btn-primary"><i class="ft-user icon-left"></i>{{ tr('view_product_sub_categories') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.product_sub_categories.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="product_sub_category_id" id="product_sub_category_id" value="{{ $product_sub_category->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('product_sub_category_name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $product_sub_category->name ?: old('name') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                             <label for="title">{{ tr('select_product_category') }} <span class="admin-required">*</span> </label>
                                            <select class="form-control select2" id="product_category_id" name="product_category_id" required>
                                            <option value="">{{tr('select_product_category')}}</option>
                                            @foreach($product_categories as $product_category)
                                                <option class="select-color" value="{{$product_category->id}}"@if($product_category->is_selected == YES) selected @endif >
                                                    {{$product_category->name}}
                                                </option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ tr('select_picture') }}</label>
                                        <input type="file" class="form-control"  id="picture" name="picture" accept="image/png,image/jpeg" src="{{ $product_sub_category->picture ? $product_sub_category->picture : asset('placeholder.png') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-12"> 

                                    <div class="form-group">

                                        <label for="description">{{tr('description')}}</label>

                                        <textarea rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}">{{old('description') ?: $product_sub_category->description}}</textarea>

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

