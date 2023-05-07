@extends('layouts.admin')

@section('title', tr('stories'))

@section('content-header', tr('stories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.stories.index')}}">{{tr('stories')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('edit_story')}}</a></li>

@endsection

@section('content')

    @include('admin.stories._form')

@endsection

@section('scripts')

<script type="text/javascript">

	function select_image_type(){
		
         if($('input[name="file_type"]:checked').val() == 'video'){
			$('.preview_file').css("display", "none");
		}else{
			$('.preview_file').css("display", "none");
		}
		
	}

</script>

@endsection