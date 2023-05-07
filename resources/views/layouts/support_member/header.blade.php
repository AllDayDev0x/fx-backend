
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu fixed-top navbar-semi-dark navbar-shadow">

    <div class="navbar-wrapper">

        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                <li class="nav-item">
                    <a class="navbar-brand" href="{{route('support_member.dashboard')}}">
                        <h3 class="brand-text">{{Setting::get('site_name')}}</h3>
                    </a>
                </li>
                <li class="nav-item d-md-none">
                    <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="fa fa-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>

        <div class="navbar-container content">
            
            <div class="collapse navbar-collapse" id="navbar-mobile">
                
                <ul class="nav navbar-nav mr-auto float-left">

                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#">
                            <i class="ft-menu"></i></a>
                    </li>
                    
                </ul>

                <ul class="nav navbar-nav float-right">
               
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            
                            <span class="avatar">
                                <img src="{{ Auth::guard('support_member')->user()->picture ? Auth::guard('support_member')->user()->picture : asset('placeholder.png')}}">
                                <i></i>
                            </span>

                            <span class="user-name">{{Auth::guard('support_member')->user()->name}}</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{route('support_member.profile')}}">
                                <i class="ft-user"></i> {{tr('account')}}
                            </a>
                            
                            <a class="dropdown-item"  data-toggle="modal" data-target="#logoutModel" href="{{route('support_member.logout')}}"><i class="ft-power"></i>{{tr('logout')}}</a>
                        </div>

                    </li>

                </ul>

            </div>

        </div>

    </div>

</nav>