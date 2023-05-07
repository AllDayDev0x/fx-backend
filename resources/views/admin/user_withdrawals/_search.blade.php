<form method="GET" action="{{route('admin.user_withdrawals')}}">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6">
        
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">

            <select class="form-control select2" name="status">

                <option class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{WITHDRAW_INITIATED}}" @if(Request::get('status') == WITHDRAW_INITIATED && Request::get('status') !='') selected  @endif>{{tr('initiated')}}</option>

                <option class="select-color" value="{{WITHDRAW_PAID}}" @if(Request::get('status') == WITHDRAW_PAID && Request::get('status') !='') selected  @endif>{{tr('paid')}}</option>

                <option class="select-color" value="{{WITHDRAW_DECLINED}}" @if(Request::get('status') == WITHDRAW_DECLINED && Request::get('status') !='') selected  @endif>{{tr('rejected')}}</option>

            </select>
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}"
                placeholder="{{tr('user_withdrawals_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default reset-btn">
                   <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                
                <a class="btn btn-default reset-btn" href="{{route('admin.user_withdrawals')}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>
<br>