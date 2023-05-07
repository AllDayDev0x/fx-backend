<form method="GET" action="{{route('admin.audio_call_requests.index')}}">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">
            
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">
          
            <select class="form-control select2" name="status">

                <option  class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{AUDIO_CALL_REQUEST_SENT}}" @if(Request::get('status') == AUDIO_CALL_REQUEST_SENT && Request::get('status')!='' ) selected @endif>{{tr('VIDEO_CALL_REQUEST_SENT')}}</option>

                <option class="select-color" value="{{AUDIO_CALL_REQUEST_ACCEPTED}}" @if(Request::get('status') == AUDIO_CALL_REQUEST_ACCEPTED && Request::get('status')!='' ) selected @endif>{{tr('VIDEO_CALL_REQUEST_ACCEPTED')}}</option>

                <option class="select-color" value="{{AUDIO_CALL_REQUEST_REJECTED}}" @if(Request::get('status') == AUDIO_CALL_REQUEST_REJECTED && Request::get('status')!='' ) selected @endif>{{tr('VIDEO_CALL_REQUEST_REJECTED')}}</option>

                <option class="select-color" value="{{AUDIO_CALL_REQUEST_JOINED}}" @if(Request::get('status') == AUDIO_CALL_REQUEST_JOINED && Request::get('status')!='' ) selected @endif>{{tr('VIDEO_CALL_REQUEST_JOINED')}}</option>

                <option class="select-color" value="{{AUDIO_CALL_REQUEST_ENDED}}" @if(Request::get('status') == AUDIO_CALL_REQUEST_ENDED && Request::get('status')!='' ) selected @endif>{{tr('VIDEO_CALL_REQUEST_ENDED')}}</option>

            </select>

        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('call_request_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.audio_call_requests.index')}}" class="btn btn-default reset-btn">
                        <span> <i class="fa fa-eraser" aria-hidden="true"></i>
                        </span>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>