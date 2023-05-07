@extends('layouts.admin.focused')

@section('title', tr('login'))

@section('content-header', tr('login'))

@section('content')

<div class="sign-up-page">

    <div class="row">

        <div class="col-md-12 col-xl-6 col-lg-6 padding-left-zero">

            <div class="sign-up-left-content text-center">

                <h2>{{Setting::get('site_name')}}</h2>

                <p>{{Setting::get('tag_name')}}</p>

                <!-- <img src="{{asset('images/auth/sign_up.svg')}}" alt="sign-up-img"/> -->
                <div class="login-slider owl-carousel">

                    <div>
                        <img src="{{asset('images/auth/sign_up.svg')}}" alt="sign-up-img"/>
                    </div>
                    <div>
                        <img src="{{asset('images/auth/login-2.svg')}}" alt="sign-up-img"/>
                    </div>
                    <div>
                        <img src="{{asset('images/auth/login-3.svg')}}" alt="sign-up-img"/>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-12 col-xl-6 col-lg-6">

            <div class="sign-up-right-content">

                @include('notifications.notify')

                <div class="sign-rignt-img">

                <img src="{{Setting::get('site_logo')}}" alt="logo-img"/>

                </div>

                <div class="sign-up-content">
                    <h2>{{tr('login')}}</h2>
                </div>

                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.login.post') }}"  autocomplete="new-password">

                    @csrf

                    <input type="hidden" name="timezone" value="" id="userTimezone">

                    <div class="form-group">

                        <label for="exampleInputEmail1">{{tr('email_address_field')}}</label>

                        <input type="email" class="form-control" id="exampleInputEmail1" required placeholder="{{tr('email_address')}}" value="{{old('email') ?: Setting::get('demo_admin_email')}}" name="email" pattern="^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$" oninvalid="this.setCustomValidity(&quot;{{ tr('email_validate') }}&quot;)" oninput="this.setCustomValidity('')" aria-describedby="emailHelp">

                    </div>
                   
                    <div class="form-group">

                        <label for="exampleInputPassword1">{{tr('password')}}</label>

                        <input name="password" type="password" class="form-control" id="exampleInputPassword1" placeholder="{{tr('enter_password')}}" required minlength="6" maxlength="64" title="Enter Minimum 6 character" value="{{old('password') ?: Setting::get('demo_admin_password')}}" autocomplete="off">

                    </div>

                    <div class="form-group recaptcha_div" style="display : none;">

                         {!! NoCaptcha::renderJs() !!}
                         {!! NoCaptcha::display() !!}

                    </div>

                    <button type="submit" class="btn btn-primary login-btn ">{{tr('login')}}</button>

                    <div class="forgot-btn">
                        <!-- <a href="">{{tr('forgot_password')}}</a> -->
                    </div>

                </form>

            </div>

        </div>
        
    </div>
       
</div>

@endsection

@section('scripts')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>

<script src="{{asset('js/bootstrap.min.js')}}"></script>

<script src="{{asset('js/jquery.min.js')}}"></script>

<script src="{{asset('js/popper.min.js')}}"></script>

<script src="{{asset('js/jstz.min.js')}}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js"></script>

<script>
    $(document).ready(function() {
        var dMin = new Date().getTimezoneOffset();
        var dtz = -(dMin/60);
        // alert(dtz);
        $("#userTimezone").val(jstz.determine().name());

        $('.login-slider').owlCarousel({
            items: 1,
            margin: 10,
            lazyLoad: true,
            loop:true,
            dots: true,
            autoplay:true,
            autoplaySpeed:3000,
            nav:false,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 1,
                },
                1000: {
                    items: 1,
                }
            }
        });
    });

    window.onload = function() {
        @php $is_captcha_enabled = Setting::get('is_captcha_enabled'); @endphp

        var is_captcha_enabled = "{{$is_captcha_enabled}}";

        if(is_captcha_enabled == 1){
            
            $('.recaptcha_div').css("display", "block");

            var $recaptcha = document.querySelector('#g-recaptcha-response');

            if($recaptcha) {
                $recaptcha.setAttribute("required", "required");
            }
        }
    };

</script>

@endsection

