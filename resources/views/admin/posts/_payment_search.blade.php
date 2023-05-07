<form method="GET" action="{{route('admin.post.payments')}}" class="form-bottom">

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">
            @if(Request::has('search_key'))
                <p class="text-muted">{{tr('search_results_for')}}<b>{{Request::get('search_key')}}</b></p>
            @endif
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="hidden" name="post_id" value="{{Request::get('post_id')??''}}">
               
                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')}}"
                placeholder="{{tr('post__payment_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default reset-btn">
                   <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                
                <a  href="{{route('admin.post.payments',['post_id'=>Request::get('post_id')??''])}}" class="btn btn-default reset-btn"><i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>
