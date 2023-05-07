@if(Session::has('flash_error'))

    <div class="alert alert-danger alert-dismissible mb-2 text-capitalize" role="alert">
	  	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    	<span aria-hidden="true">&times;</span>
	  	</button>
	  	{{Session::get('flash_error')}}
	</div>
@endif


@if(Session::has('flash_success'))

   <div class="alert alert-success alert-dismissible mb-2 text-capitalize" role="alert">
	  	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    	<span aria-hidden="true">&times;</span>
	  	</button>
	  	{{Session::get('flash_success')}}
	</div>

@endif
