
@extends('layouts.support_member.focused')

@section('content')

<section class="flexbox-container">

    <div class="col-12 d-flex align-items-center justify-content-center">

        <div class="col-md-4 col-10 box-shadow-2 p-0">

            <div class="card border-grey border-lighten-3 px-1 py-1 m-0">

                <div class="card-header border-0">
                    <div class="card-title text-center">
                        <img src="{{Setting::get('site_icon')}}" alt="{{Setting::get('site_name')}}" style="width: 90px;height: 90px;border-radius: 1em">
                    </div>
                    
                    <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                        <span>{{Setting::get('site_name')}}</span>
                    </h6>
                </div>

                <div class="card-content">

                    <div class="card-body">

                        @include('notifications.notify')

							<form class="pt-2" @if($is_email_configured == YES) action="{{route('support_member.password.email')}}" method="POST" @endif>

			                    @csrf

			                    @if ($errors->has('email'))
								    <div class="text-danger">{{ $errors->first('email') }}</div>
								@endif

								@if($is_email_configured == NO)

									<div class="text-danger">{{tr('email_not_configured')}}</div>

								@endif

								<fieldset class="form-group position-relative has-icon-left">
                                
	                                <input type="email" name="email" class="form-control" id="email" placeholder="{{ tr('email') }}" value="{{old('email')}}" required>
	                                
	                                <div class="form-control-position">
	                                    <i class="ft-user"></i>
	                                </div>

	                            </fieldset>

			                    <div class="profile-edit-btn mt-4">

			                    	@if($is_email_configured == YES)
			                    		<input type="submit" name="submit" value="{{tr('submit')}}" class="btn btn-block btn-primary mr-2">
			                    	@else
			                    		<input type="button" disabled value="{{tr('submit')}}" class="btn btn-block btn-primary mr-2">
			                    	@endif
			                    </div>

			                    <div class="register-footer mt-3 text-center">
	                                <p>
	                                    {{tr('already_have_account')}}
	                                    <b class="pl-2"><a class="text-uppercase login-color"href="{{route('support_member.login')}}" role="button">{{tr('login')}}</a> 
	                                    </b>
	                                </p>
	                            </div>
			                </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
	