@extends('layouts.admin.focused')

@section('title', tr('login'))

@section('content-header', tr('login'))

@section('content')

<div class="sign-up-page">
        <div class="row">
            <div class="col-md-6 padding-left-zero">
                <div class="sign-up-left-content text-center">
                    <h2>Partner Program</h2>
                    <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Odit, minus excepturi. Itaque fugiat aliquam cumque, impedit error eveniet culpa ipsum, sed aperiam atque perferendis temporibus quod a nisi, unde eaque.</p>
                    <img src="images/auth/sign_up.svg" alt="sign-up-img"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="sign-up-right-content">
                    <div class="sign-rignt-img">
                    <img src="images/auth/logo-color.svg" alt="logo-img"/>

                    </div>
                    <div class="sign-up-content">
                        <h2>Login</h2>
                    </div>
                    <form>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                        </div>
                       
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" class="form-control" id="exampleInputPassword1">
                        </div>
                      
                        <button type="submit" class="btn btn-primary login-btn ">Login</button>
                        <div class="forgot-btn">
                        <!-- <a href="forgotpassword.html">Forgot Password</a> -->

                        </div>
                    </form>
                </div>
            </div>
        </div>
       
    </div>

@endsection

@section('scripts')
<script src="{{asset('js/bootstrap.min.js')}}"></script>

<script src="{{asset('js/jquery.min.js')}}"></script>

<script src="{{asset('js/popper.min.js')}}"></script>

<script src="{{asset('js/jstz.min.js')}}"></script>

<script>
    $(document).ready(function() {
        var dMin = new Date().getTimezoneOffset();
        var dtz = -(dMin/60);
        // alert(dtz);
        $("#userTimezone").val(jstz.determine().name());
    });

</script>

@endsection

