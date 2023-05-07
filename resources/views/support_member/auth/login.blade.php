@extends('layouts.support_member.focused')

@section('title', tr('login'))

@section('content-header', tr('login'))

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

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('support_member.login.post') }}" novalidate>

                            @csrf

                            <fieldset class="form-group position-relative has-icon-left">
                                
                                <input type="text" class="form-control" id="user-name" placeholder="Your Username" required value="{{old('email') ?: Setting::get('demo_support_member_email')}}" name="email">
                                
                                <div class="form-control-position">
                                    <i class="ft-user"></i>
                                </div>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif

                            </fieldset>
                            <fieldset class="form-group position-relative has-icon-left">
                                <input name="password" type="password" class="form-control" id="user-password" placeholder="Enter Password" required minlength="6" maxlength="64" title="Enter Minimum 6 character" value="{{old('password') ?: Setting::get('demo_support_member_password')}}">
                                <div class="form-control-position">
                                    <i class="fa fa-key"></i>
                                </div>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </fieldset>
                            <button type="submit" class="btn btn-outline-primary btn-block"><i class="ft-unlock"></i> Login</button>

                            <div class="register-footer mt-3 text-center">
                                <p>
                                    <b><a href="{{route('support_member.password.request')}}" class=" text-uppercase">{{tr('forgot_password')}}</a></b>
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