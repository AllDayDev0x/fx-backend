<form method="GET" action="{{route('admin.user_tips.index')}}" class="">

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6">
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width"></div>
        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="hidden" id="user_id" name="user_id" value="{{Request::get('user_id') ?? ''}}">

                <input type="hidden" id="post_id" name="post_id" value="{{Request::get('post_id') ?? ''}}">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('user_tip_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.user_tips.index',['user_id'=>$user->id??'','post_id'=>$post->id??''])}}" class="btn btn-default reset-btn">
                        <span> <i class="fa fa-eraser" aria-hidden="true"></i>
                        </span>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>