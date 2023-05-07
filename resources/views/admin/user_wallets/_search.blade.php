<form method="GET" action="{{route('admin.user_wallets.index')}}">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" name="search_key"
                placeholder="{{tr('user_wallet_search_placeholder')}}" value="{{Request::get('search_key')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default">
                   <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                </button>

                <a href="{{route('admin.user_wallets.index')}}" class="btn btn-default reset-btn">
                        <i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>
<br>