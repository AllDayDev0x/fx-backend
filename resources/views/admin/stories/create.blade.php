@extends('layouts.admin')

@section('title', tr('stories'))

@section('content-header', tr('stories'))

@section('styles')

    <link rel="stylesheet" href="{{asset('admin-assets/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">

@endsection


@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{route('admin.stories.index')}}">{{tr('stories')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('create_post')}}</a></li>


@endsection

@section('content')

    @include('admin.stories._form')

@endsection


@section('scripts')


<script type="text/javascript">


    $(document).ready(function() {
        $('#summernote').summernote();
    });
			
	function select_image_type(){
		
        if($('input[name="file_type"]:checked').val() == 'video'){
			$('.preview_file').css("display", "none");
			$('.upload_file').css("display", "block");
			$("#upload").attr("accept", $('input[name="file_type"]:checked').val()+"/*");
		}else{
			$('.preview_file').css("display", "none");
			$('.upload_file').css("display", "block");
			$("#upload").attr("accept", $('input[name="file_type"]:checked').val()+"/*");		
		}
		
	}

</script>

@endsection