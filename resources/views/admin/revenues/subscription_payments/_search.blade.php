
<form method="GET" action="{{route('admin.subscription_payments.index')}}">

    <div class="row">

        <div class="col-6">
            @if(Request::has('search_key'))
                <p class="text-muted">{{tr('search_results_for')}}<b>{{Request::get('search_key')}}</b></p>
            @endif
        </div>

        <div class="col-6">

            <div class="input-group">
               
                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')}}"
                placeholder="{{tr('subscription_payment_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default">
                   <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                </button>
                
                <button class="btn btn-default"><a  href="{{route('admin.subscription_payments.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>
<br>