<!DOCTYPE html>

<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <meta name="description" content="{{Setting::get('site_name')}}">

    <meta name="keywords" content="{{Setting::get('site_name')}}">
    
    <meta name="author" content="{{Setting::get('site_name')}}">
    
    <title>{{Setting::get('site_name')}}</title>  

    <meta name="robots" content="noindex">

    <link rel="apple-touch-icon" href="@if(Setting::get('site_logo')) {{ Setting::get('site_logo') }}  @else {{asset('admin-assets/images/ico/apple-icon-120.png') }} @endif">

    <link rel="shortcut icon" type="image/x-icon" href="{{Setting::get('site_icon')}}">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.11/css/all.css" integrity="sha384-p2jx59pefphTFIpeqCcISO9MdVfIm4pNnsL08A6v5vaQc4owkQqxMV8kg4Yvhaw/" crossorigin="anonymous">
    
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap-extend.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('css/master_style.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('css/_all-skins.css')}}">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
    
        .login_bg-image {
          background-image: url("/images/admin-bg.jpg");
          background-size: cover;
        }

    </style>

</head>

<body class="hold-transition login-page">

    <section class="h-p100">
        <div class="container h-p100">
            <div class="">
                @yield('content')
            </div>
        </div>
    </section>
    
    @yield('scripts')
    
</body>

</html>