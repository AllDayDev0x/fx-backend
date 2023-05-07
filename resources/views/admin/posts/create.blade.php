@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('styles')

    <link rel="stylesheet" href="{{asset('admin-assets/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">

@endsection


@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{route('admin.posts.index')}}">{{tr('posts')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('create_post')}}</a></li>


@endsection

@section('content')

    @include('admin.posts._form')

@endsection


@section('scripts')

<script src="{{asset('js/summernote.min.js')}}"></script>

<script type="text/javascript">


    $(document).ready(function() {
        $('#summernote').summernote();
    });
			
	function select_image_type(){
		
        if($('input[name="file_type"]:checked').val() == 'video'){
			$('.preview_file').css("display", "block");
			$('.upload_file').css("display", "block");
			$('.video_preview_file').css("display", "block");
			$('.post_amount').css("display", "block");
			console.log($('input[name="file_type"]:checked').val()+"/*");
			$("#upload").attr("accept", $('input[name="file_type"]:checked').val()+"/*");
		}else{
			$('.video_preview_file').css("display", "none");
			$('.preview_file').css("display", "none");
			$('.upload_file').css("display", "block");
			$('.post_amount').css("display", "block");
			console.log($('input[name="file_type"]:checked').val()+"/*");
			$("#upload").attr("accept", $('input[name="file_type"]:checked').val()+"/*");		
		}
		
	}

</script>

@endsection