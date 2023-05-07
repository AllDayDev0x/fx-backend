@extends('layouts.admin')

@section('title', tr('edit_user_product'))

@section('content-header', tr('user_products'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.user_products.index')}}">{{tr('user_products')}}</a></li>

    <li class="breadcrumb-item active">{{tr('edit_user_product')}}</a></li>

@endsection

@section('content')

    @include('admin.user_products._form')

@endsection

@section('scripts')

<script src="{{asset('js/summernote.min.js')}}"></script>

<script  type="text/javascript">

    $(document).ready(function() {

        var id = $("#user_id").val();

        if(id !=''){

            $("#image_preview").show();

        } else {

            $("#image_preview").hide();
        }

    });
 
    $(document).ready(function() {
        $('#summernote').summernote();
    });
</script>

@endsection
