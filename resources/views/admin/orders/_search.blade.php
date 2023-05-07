<form method="GET" action="{{route('admin.orders.index')}}">

    <div class="row search-form">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
            
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">

            <select class="form-control select2" name="status">

                <option  class="select-color" value="">{{tr('select_status')}}</option>

                <option  class="select-color" value="{{SORT_BY_ORDER_PLACED}}" @if(Request::get('status') == SORT_BY_ORDER_PLACED) selected="true" @endif>{{tr('order_placed')}}</option>

                <option  class="select-color" value="{{SORT_BY_ORDER_PACKED}}" @if(Request::get('status') == SORT_BY_ORDER_PACKED) selected="true" @endif>{{tr('order_packed')}}</option>

                <option  class="select-color" value="{{SORT_BY_ORDER_SHIPPED}}" @if(Request::get('status') == SORT_BY_ORDER_SHIPPED) selected="true" @endif>{{tr('order_shipped')}}</option>

                <option  class="select-color" value="{{SORT_BY_ORDER_DELIVERD}}" @if(Request::get('status') == SORT_BY_ORDER_DELIVERD) selected="true" @endif>{{tr('order_deliverd')}}</option>

                <option  class="select-color" value="{{SORT_BY_ORDER_CANCELLED}}" @if(Request::get('status') == SORT_BY_ORDER_CANCELLED) selected="true" @endif>{{tr('order_cancelled')}}</option>

            </select>

        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" name="search_key"
                placeholder="{{tr('orders_search_placeholder')}}" value="{{Request::get('search_key')??''}}"> <span class="input-group-btn">
                &nbsp
         
                <input type="hidden" name="user_id" value="{{Request::get('user_id') ?? ''}}">

                <button type="submit" class="btn btn-default">
                   <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                </button>

                <a href="{{route('admin.orders.index',['user_id'=>Request::get('user_id')??''])}}" class="btn btn-default reset-btn">
                        <i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form></br>