
<form method="GET" action="{{route('admin.product_inventories.index')}}">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">
            @if(Request::has('search_key'))
                <p class="text-muted">{{tr('search_results_for')}}<b>{{Request::get('search_key')}}</b></p>
            @endif
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" name="search_key"
                placeholder="{{tr('product_inventory_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default">
                   <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                </button>
                
                <button class="btn btn-default"><a  href="{{route('admin.user_products.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>
<br>