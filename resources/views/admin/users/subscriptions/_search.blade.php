<form method="GET" action="{{route('admin.users_subscriptions.index')}}" class="">

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6">
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">

            <!-- <select class="form-control select2" name="status">

                <option class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{SORT_BY_FREE_SUBSCRIPTION}}" @if(Request::get('status') == SORT_BY_FREE_SUBSCRIPTION && Request::get('status')!='' ) selected @endif>{{tr('sort_by_free')}}</option>

                <option class="select-color" value="{{SORT_BY_PAID_SUBSCRIPTION}}" @if(Request::get('status') == SORT_BY_PAID_SUBSCRIPTION && Request::get('status')!='' ) selected @endif>{{tr('sort_by_paid')}}</option>

            </select> -->
        </div>

        
        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12 pull-right">

            <div class="input-group">

                <input type="hidden" class="form-control" name="from_user_id" value="{{$user->id ?? ''}}">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('user_subscriptions_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.users_subscriptions.index',['from_user_id'=>Request::get('from_user_id')])}}" class="btn btn-default reset-btn">
                        <span> <i class="fa fa-eraser" aria-hidden="true"></i>
                        </span>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>