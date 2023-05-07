
<form method="GET" action="{{route('admin.order_products')}}">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="hidden" id="user_product_id" name="user_product_id" value="{{Request::get('user_product_id') ?? ''}}">
               
                <input type="text" class="form-control" value="{{Request::get('search_key')}}" name="search_key"
                placeholder="{{tr('order_product_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default">
                   <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                </button>
                
                <a href="{{route('admin.order_products',['user_product_id'=>Request::get('user_product_id')])}}" class="btn btn-default reset-btn">
                    <i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>
<br>