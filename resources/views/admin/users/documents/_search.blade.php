<form method="GET" action="{{route('admin.user_documents.index')}}">

<div class="row">

    <div class="col-xs-12 col-sm-12 col-lg-4 col-md-6 resp-mrg-btm-md">
    </div>

    <div class="col-xs-12 col-sm-12 col-lg-6 mx-auto col-md-12">

        <div class="input-group form-margin-left-sm">

            <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('users_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default reset-btn">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>

                <a href="{{route('admin.user_documents.index')}}" class="btn btn-default reset-btn">
                    <i class="fa fa-eraser" aria-hidden="true"></i>
                </a>

            </span>

        </div>

    </div>

</div>

</form>

<br>
