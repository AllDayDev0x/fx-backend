<form class="col-6 row pull-right" action="{{route('admin.product_sub_categories.index')}}" method="GET" role="search">
    <div class="input-group">

        <input type="hidden" name="category_id" value="{{Request::get('category_id')??''}}">

        <input type="text" class="form-control" name="search_key"  value="{{Request::get('search_key')??''}}"
        placeholder="{{tr('product_sub_categories_search_placeholder')}}" required> 
        <span class="input-group-btn">
            &nbsp
            <button type="submit" class="btn btn-default">
                <i class="fa fa-search" aria-hidden="true"></i>
            </button>
            <a href="{{route('admin.product_sub_categories.index',['category_id'=> Request::get('category_id') ?? ''])}}" class="btn btn-default reset-btn">
                <span class=""> <i class="fa fa-eraser" aria-hidden="true"></i>
                </span>
            </a>
        </span>
    </div>
</form>