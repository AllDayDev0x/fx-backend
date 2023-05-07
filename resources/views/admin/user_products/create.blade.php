@extends('layouts.admin')

@section('title', tr('user_products'))

@section('content-header', tr('user_products'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.user_products.index')}}">{{tr('user_products')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_user_product')}}</a></li>

@endsection

@section('content')

    @include('admin.user_products._form')

@endsection

@section('scripts')

<script src="{{ asset('admin-assets/plugins/jquery-steps/build/jquery.steps.min.js')}}"></script>
 
<script src="{{ asset('admin-assets/plugins/jquery-validation/dist/jquery.validate.min.js')}}"></script>

<script src="{{ asset('admin-assets/js/wizard.js')}}"></script>

<script src="{{asset('js/summernote.min.js')}}"></script>

<script>

    $(document).ready(function() {

        $('#product_category_id').on('change' , function (e) {

            var category_id = $(this).val();

            var sub_category_url = "{{route('admin.get_product_sub_categories')}}";

            var data = {'product_category_id' : category_id, _token: '{{csrf_token()}}'};

            var request = $.ajax({
                            url: sub_category_url,
                            type: "POST",
                            data: data,
                        });

            request.done(function(result) {

                if(result.success == true) {
                    $("#product_sub_category_id").html(result.view);

                    $("#product_sub_category_id").select2();
                }

            });

            request.fail(function(jqXHR, textStatus) {
                alert( "Request failed: " + textStatus );
            });

        });


    });

    $(document).ready(function() {
        $('#summernote').summernote();
    });
</script>

@endsection