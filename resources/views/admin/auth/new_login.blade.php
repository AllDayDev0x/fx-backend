@extends('layouts.admin.focused')

@section('title', tr('login'))

@section('content-header', tr('login'))

@section('content')

    <div class="col-lg-5 col-md-8 col-12">

        @include('notifications.notify')

        <div class="login-box-sec">
            <form>
                <h4 class="title">Login</h4>
                <label>User Name</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Type your username" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <label>Password</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Type your password" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <div class="login-btn-sec">
                    <a href="#" class="login-btn">
                        Login
                    </a>
                </div>
            </form>
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

