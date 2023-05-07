@extends('layouts.admin')

@section('title', tr('add_user'))

@section('content-header', tr('users'))

@section('breadcrumb')

    
    
    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('users')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_user')}}</a></li>

@endsection

@section('content')

    @include('admin.users._form')

@endsection

@section('scripts')

<script type="text/javascript">
		
	function premium_check(){
         
         if($('input[name="user_account_type"]:checked').val() == {{USER_PREMIUM_ACCOUNT}}){
			$('.premium_account').css("display", "block");
		}else{
			$('.premium_account').css("display", "none");
		}
		
	}

</script>
@endsection