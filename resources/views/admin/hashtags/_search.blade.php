<form method="GET" action="{{route('admin.hashtags.index')}}">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
            @if(Request::has('search_key'))
            <p class="text-muted">Search results for <b>{{Request::get('search_key')}}</b></p>
            @endif
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">

            <select class="form-control select2" name="status">

                <option class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{SORT_BY_APPROVED}}" @if(Request::get('status') == SORT_BY_APPROVED && Request::get('status')!='' ) selected @endif>{{tr('approved')}}</option>

                <option class="select-color" value="{{SORT_BY_DECLINED}}" @if(Request::get('status') == SORT_BY_DECLINED && Request::get('status')!='' ) selected @endif>{{tr('declined')}}</option>

            </select>

        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('hashtag_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.hashtags.index')}}" class="btn btn-default reset-btn">
                        <span> <i class="fa fa-eraser" aria-hidden="true"></i>
                        </span>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>