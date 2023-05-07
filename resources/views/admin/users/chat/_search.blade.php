<form method="GET" action="{{route('admin.chat_asset_payments.index')}}" class="form-bottom">

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
            @if(Request::has('search_key'))
            <p class="text-muted">Search results for <b>{{Request::get('search_key')}}</b></p>
            @endif
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md"></div>
        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('user_subscriptions_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.chat_asset_payments.index')}}" class="btn btn-default reset-btn">
                        <i class="fa fa-eraser" aria-hidden="true"></i>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>