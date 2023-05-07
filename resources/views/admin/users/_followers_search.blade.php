<form method="GET" action="{{route('admin.user_followers')}}">

    <div class="row">

        <div class="col-md-3"></div>
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
            <input type="hidden" id="user_id" name="user_id" value="{{Request::get('user_id') ?? ''}}">
        </div>



        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('search_by_follower_name')}}">

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.user_followers',['user_id'=>Request::get('user_id')])}}" class="btn btn-default reset-btn">
                        <span> <i class="fa fa-eraser" aria-hidden="true"></i>
                        </span>
                    </a>
                 
                </span>

            </div>

        </div>

    </div>

</form>
<br>