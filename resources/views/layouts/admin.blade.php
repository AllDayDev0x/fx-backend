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
  
    <!-- Bootstrap 4.0-->
	<link rel="stylesheet" href="{{asset('admin-assets/vendors/bootstrap/dist/css/bootstrap.css')}}">
	
	<!-- Bootstrap-extend -->
	<link rel="stylesheet" href="{{asset('admin-assets/css/bootstrap-extend.css')}}">
	
	<!-- Morris charts -->
	<link rel="stylesheet" href="{{asset('admin-assets/vendors/morris.js/morris.css')}}">
	
	<!-- weather weather -->
	<link rel="stylesheet" href="{{asset('admin-assets/vendors/weather-icons/weather-icons.css')}}">
	
	<!-- date picker -->
	<link rel="stylesheet" href="{{asset('admin-assets/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker.css')}}">
	
	<!-- daterange picker -->
	<link rel="stylesheet" href="{{asset('admin-assets/vendors/bootstrap-daterangepicker/daterangepicker.css')}}">
	
	<!-- bootstrap wysihtml5 - text editor -->
	<link rel="stylesheet" href="{{asset('admin-assets/vendors/bootstrap-wysihtml5/bootstrap3-wysihtml5.css')}}">
	
	<!-- theme style -->
	<link rel="stylesheet" href="{{asset('admin-assets/css/master_style.css')}}">
	
	<!-- Unique_Admin skins -->
	<link rel="stylesheet" href="{{asset('admin-assets/css/skins/_all-skins.css')}}">

    <link rel="stylesheet" href="{{asset('admin-assets/css/custom.css')}}">
	
    <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">

    <!-- bootstrap slider -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/bootstrap-slider/slider.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/bootstrap-slider/style.min.css')}}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('admin-assets/css/bootstrap-datetimepicker.min.css')}}">
  
  
    @yield('styles')

    <style>
        .white-space-nowrap{
            white-space: nowrap;
        }

        .main-menu.menu-dark .navigation {
            background: #fff;
        }

        .main-menu.menu-dark .navigation li a {
            color: #403e3e
        }
        .main-menu.menu-dark .navigation > li.hover > a, .main-menu.menu-dark .navigation > li:hover > a, .main-menu.menu-dark .navigation > li.active > a {
            background: var(--primary-color)
        }

        .main-menu.menu-dark .navigation > li > ul {
            background: transparent;
        }

        .main-menu.menu-dark .navigation > li ul .active > a {
            color: var(--primary-color)
        }

        .main-menu.menu-dark .navigation > li.open .hover > a {
            background: transparent;
        }

    </style>


</head>

<body class="hold-transition skin-purple sidebar-mini">
    
<div class="wrapper">

    @include('layouts.admin.header')

    @include('layouts.admin.sidebar')

    <div class="content-wrapper">
        

    <section class="content-header">
      <h1>
          @yield('content-header')
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        @yield('breadcrumb')
      </ol>
    </section>
        
                                   
           
            
            @include('notifications.notify')

            <div class="content-body">

                @yield('content')
                
            </div>
        </div>
    
    </div>
    </div>

    @include('layouts.admin.footer')

    @include('layouts.admin._logout_model')

    @include('layouts.admin.scripts')

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

        if ($('#editor').length) {
            ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .then( editor => {
                    console.log( editor );
            } )
            .catch( error => {
                    console.error( error );
            } );
        } else {
            /* it doesn't exist */
        }
            
    </script>

    <script type="text/javascript">

        $(document).ready(function(){
       
            setTimeout(function(){

            $('#DataTables_Table_0_filter').hide();

                $('#DataTables_Table_0_filter').hide();

                $('#DataTables_Table_0_info').hide();
                
             },100),

            setTimeout(function(){

            $('#DataTables_Table_0_length').hide();

                $('#DataTables_Table_0_length').hide();
                
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