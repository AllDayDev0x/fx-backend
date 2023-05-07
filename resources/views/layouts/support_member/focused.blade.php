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
    
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/vendors.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/forms/icheck/icheck.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/forms/icheck/custom.css')}}">
    
    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/app.min.css')}}">
   
    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/core/menu/menu-types/vertical-menu.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/pages/login-register.min.css')}}">

</head>

<body class="vertical-layout vertical-menu 1-column bg-full-screen-image menu-expanded blank-page blank-page" data-open="click" data-menu="vertical-menu" data-col="1-column">

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>

    @include('layouts.support_member.scripts')
    
</body>

</html>