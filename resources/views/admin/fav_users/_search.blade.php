  <form method="GET" action="{{route('admin.fav_users.index')}}" class="form-bottom">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12"></div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" value="{{Request::get('search_key')}}" name="search_key"
                placeholder="{{tr('fav_user_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <input type="hidden" id="user_id" name="user_id" value="{{Request::get('user_id') ?? ''}}">

                <button type="submit" class="btn btn-default reset-btn">
                  <i class="fa fa-search" aria-hidden="true"></i>
                </button>

                <a class="btn btn-default reset-btn" href="{{route('admin.fav_users.index',['user_id'=>$user->id??''])}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>