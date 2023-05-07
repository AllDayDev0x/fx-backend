<form class="col-6 row pull-right" action="{{route('admin.referrals.index')}}" method="GET" role="search">
    <div class="input-group">
        <input type="text" class="form-control" name="search_key"  value="{{Request::get('search_key')??''}}"
        placeholder="{{tr('referrals_search_placeholder')}}" required> 
        <span class="input-group-btn">
            &nbsp
            <button type="submit" class="btn btn-default">
                <i class="fa fa-search" aria-hidden="true"></i>
            </button>
            <a href="{{route('admin.referrals.index')}}" class="btn btn-default reset-btn">
                <span class=""> <i class="fa fa-eraser" aria-hidden="true"></i>
                </span>
            </a>
        </span>
    </div>
</form>
                    