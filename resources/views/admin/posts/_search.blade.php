
<form method="GET" action="{{route('admin.posts.index')}}">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">
            <select class="form-control select2" name="paid_status">

                <option  class="select-color" value="">{{tr('select_payment_status')}}</option>

                <option  class="select-color" value="{{PAID}}" @if(Request::get('paid_status') == PAID && Request::get('paid_status')!='' ) selected @endif>{{tr('paid_post')}}</option>

                <option  class="select-color" value="{{UNPAID}}" @if(Request::get('paid_status') == UNPAID && Request::get('paid_status')!='' ) selected @endif>{{tr('free_post')}}</option>

            </select>

        </div>

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">
          <input type="hidden" class="form-control" name="scheduled" value="{{Request::get('scheduled')}}">
            <select class="form-control select2" name="status">

                <option  class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{SORT_BY_APPROVED}}" @if(Request::get('status') == SORT_BY_APPROVED && Request::get('status')!='' ) selected @endif>{{tr('approved')}}</option>

                <option class="select-color" value="{{SORT_BY_DECLINED}}" @if(Request::get('status') == SORT_BY_DECLINED && Request::get('status')!='' ) selected @endif>{{tr('declined')}}</option>

            </select>

        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="hidden" id="user_id" name="user_id" value="{{Request::get('user_id') ?? ''}}">
                <input type="hidden" id="hashtag_id" name="hashtag_id" value="{{Request::get('hashtag_id') ?? ''}}">
               
                <input type="text" class="form-control" value="{{Request::get('search_key')??''}}" name="search_key"
                placeholder="{{tr('post_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default reset-btn">
                  <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                
               <a class="btn btn-default reset-btn" href="{{route('admin.posts.index',['user_id'=>$user->id??'','hashtag_id'=>Request::get('hashtag_id')??''])}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>
<br>