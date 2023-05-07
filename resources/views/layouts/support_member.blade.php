<!DOCTYPE html>

<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <title>{{Setting::get('site_name')}}</title>   

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <meta name="description" content="{{Setting::get('site_name')}}">
    
    <meta name="robots" content="noindex">

    <link rel="shortcut icon" type="image/x-icon" href="{{Setting::get('site_icon')}}">
    
    <link rel="apple-touch-icon" href="{{Setting::get('site_icon')}}">

    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/vendors.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/forms/icheck/icheck.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/forms/icheck/custom.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/tables/datatable/datatables.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/forms/selects/select2.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/app.min.css')}}">
   
    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/core/menu/menu-types/vertical-menu.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/pages/login-register.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/fonts/simple-line-icons/style.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/core/colors/palette-gradient.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/style.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/custom.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/plugins/forms/checkboxes-radios.min.css')}}">


    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/extensions/unslider.css">

    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/weather-icons/climacons.min.css">

    <link rel="stylesheet" type="text/css" href="../../../app-assets/fonts/meteocons/style.min.css">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/charts/morris.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/pages/timeline.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/assets/css/style.css')}}">
  
    @yield('styles')


</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-col="2-columns">
    
    @include('layouts.support_member.header')

    @include('layouts.support_member.sidebar')

    <div class="app-content content">
        
        <div class="content-wrapper">

            <div class="content-header row">
                
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top pull-right">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                    
                    <h3 class="content-header-title mb-0">

                    @yield('content-header')</h3>

                </div>

            </div>

            @include('notifications.notify')

            <div class="content-body">

                @yield('content')
                
            </div>
        </div>
    
    </div>

    @include('layouts.support_member.footer')

    @include('layouts.support_member._logout_model')

    @include('layouts.support_member.scripts')

    @yield('scripts')

    <script type="text/javascript">

        @if(isset($page)) 
            $("#{{$page}}").addClass("active");
        @endif

        @if(isset($sub_page)) 
            $("#{{$sub_page}}").addClass("active");
        @endif
        
    </script>

     <script>
            ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .then( editor => {
                    console.log( editor );
            } )
            .catch( error => {
                    console.error( error );
            } );
    </script>

    <script type="text/javascript">


        $(document).ready(function(){
       
            setTimeout(function(){

            $('#DataTables_Table_0_filter').hide();

                $('#DataTables_Table_0_filter').hide();
                
             },100),

            setTimeout(function(){
                
            $('#DataTables_Table_0_paginate').hide();

                $('#DataTables_Table_0_paginate').hide();
                
             },100);

            
         });

        

    </script>

    <?php echo Setting::get('body_scripts'); ?>

</body>

</html>