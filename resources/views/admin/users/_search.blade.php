<form method="GET" action="{{route('admin.users.index')}}">

    <div class="row">

        <input type="hidden" id="account_type" name="account_type" value="{{Request::get('account_type') ?? ''}}">
        
        <input type="hidden" id="category_id" name="category_id" value="{{Request::get('category_id') ?? ''}}">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">

            @if(Request::get('account_type') != USER_PREMIUM_ACCOUNT)

            <select class="form-control select2" name="document_status">

                <option class="select-color" value="">{{tr('select_document_status')}}</option>

                <option class="select-color" value="{{USER_DOCUMENT_APPROVED}}" @if(Request::get('document_status') == USER_DOCUMENT_APPROVED && Request::get('document_status')!='' ) selected @endif>{{tr('document_approved')}}</option>

                <option class="select-color" value="{{USER_DOCUMENT_PENDING}}" @if(Request::get('document_status') == USER_DOCUMENT_PENDING && Request::get('document_status')!='' ) selected @endif>{{tr('document_pending')}}</option>

            </select>

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

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('users_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.users.index',['account_type'=>Request::get('account_type')??'','category_id'=>Request::get('category_id')??''])}}" class="btn btn-default reset-btn">
                        <i class="fa fa-eraser" aria-hidden="true"></i>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>