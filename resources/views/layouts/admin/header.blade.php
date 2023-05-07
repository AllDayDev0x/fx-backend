<header class="main-header">
    <!-- Logo -->
    <!-- <a href="index.html" class="logo"> -->
      <!-- mini logo for sidebar mini 50x50 pixels -->
	  <!-- <b class="logo-mini">
		  <span class="light-logo"><img src="{{asset('admin-assets/images/logo-light.png')}}" alt="logo"></span>
		  <span class="dark-logo"><img src="{{asset('admin-assets/images/logo-dark.png')}}" alt="logo"></span>
	  </b> -->
      <!-- logo for regular state and mobile devices -->
      <!-- <span class="logo-lg">
		  <img src="{{asset('admin-assets/images/logo-light-text.png')}}" alt="logo" class="light-logo">
	  	  <img src="{{asset('admin-assets/images/logo-dark-text.png')}}" alt="logo" class="dark-logo">
	  </span>
    </a> -->
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
		  <!-- User Account -->
        <li class="dropdown user user-menu">

                <a class="nav-link dropdown-toggle" id="languageDropdown" href="#" data-toggle="dropdown">
                    <!-- <i class="flag-icon flag-icon-gb"></i>  -->
                  <img alt="" style="width: auto; max-height: 50px" class="img-circle img-responsive" src="{{ Auth::guard('admin')->user() ? Auth::guard('admin')->user()->picture : '' }}" />
                    {{ Auth::guard('admin')->user() ? Auth::guard('admin')->user()->name :'' }}
                </a>
                <ul class="dropdown-menu scale-up">
              <!-- User image -->
              <li class="user-header">
                <img src="{{Auth::guard('admin')->user()->picture}}" class="float-left rounded-circle" alt="User Image">

                <p>
                {{Auth::guard('admin')->user()->name}}
                  <small class="mb-5">{{Auth::guard('admin')->user()->email}}</small>
                  <a href="{{route('admin.profile')}}" class="btn btn-danger btn-sm btn-rounded">View Profile</a>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row no-gutters">
                  <div class="col-12 text-left">
                    <a href="{{route('admin.settings')}}"><i class="ion ion-settings"></i> Setting</a>
                  </div>
                  <div role="separator" class="divider col-12"></div>
                    
                  <div role="separator" class="divider col-12"></div>
                    <div class="col-12 text-left">
                    <a onclick="return confirm(&quot;{{tr('confirm_logout')}}&quot;);" href="{{route('admin.logout')}}"><i class="fa fa-power-off"></i> Logout</a>
                  </div>        
                </div>
                <!-- /.row -->
              </li>
            </ul>

              </li>
        </ul>
      </div>
    </nav>
  </header>