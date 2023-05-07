<option value="">{{tr('select_product_sub_category')}}</option>

@if(count($product_sub_categories) > 0)

	@foreach($product_sub_categories as $sub_category)

		<option value="{{$sub_category->id}}" >{{$sub_category->name}}</option>

	@endforeach

@endif