@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.posts.index')}}">{{tr('posts')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('edit_post')}}</a></li>

@endsection

@section('content')

    @include('admin.posts._form')

@endsection

@section('scripts')

<script src="{{asset('js/summernote.min.js')}}"></script>

<script type="text/javascript">


    $(document).ready(function() {

        var post_file_type = <?= json_encode($post_files[0]->file_type ?? '') ?>;

        if(post_file_type == 'video'){
			$('.preview_file').css("display", "block");
			$('.upload_file').css("display", "block");
			$('.video_preview_file').css("display", "block");
			$('.post_amount').css("display", "block");
			
		}else if(post_file_type == 'image' || post_file_type == 'audio'){
			$('.video_preview_file').css("display", "none");
			$('.preview_file').css("display", "none");
			$('.upload_file').css("display", "block");
			$('.post_amount').css("display", "block");
					
		}
        

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

	function post_edit_delete(post_file_id,post_id){

		var url = "{{route('admin.posts.file_delete')}}";

        var data = {'post_file_id' : post_file_id, 'post_id' : post_id, _token: '{{csrf_token()}}'};

		$.ajax({
          type: "POST",
          data: data,
          url: url,
          success: function (data) {

          	console.log(data);

            if(data.success == true){
                
            	var x = document.getElementById("alert-success-staging");
                x.innerHTML = data.message;
                document.getElementById("alert-success").style.display = "block";
                document.getElementById("alert-success-staging-close").style.display = "block";
                $('#show-hide-'+post_file_id).remove();

            }else{

            	var x = document.getElementById("alert-error-staging");    
                x.innerHTML = data.error_messages;
                document.getElementById("alert-error").style.display = "block";
                document.getElementById("alert-error-staging-close").style.display = "block";
                
            }
          },
          error: function(error) {
            alert("Error: " + error);
          }
        });
		
	}

</script>

@endsection