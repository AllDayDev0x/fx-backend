<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">

    <div class="main-menu-content">

        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            <li class="nav-item" id="dashboard">
                <a href="{{route('support_member.dashboard')}}">
                    <i class="fa fa-dashboard"></i>
                    <span class="menu-title" data-i18n="">{{tr('dashboard')}}</span>
                </a>
            </li>

            <li class="navigation-header">
                <span>{{tr('ticket_management')}}</span>
            </li>

            <li class="nav-item" id="dashboard">
                <a href="{{route('support_member.support_tickets.index')}}">
                    <i class="fa fa-ticket"></i>
                    <span class="menu-title" data-i18n="">{{tr('support_tickets')}}</span>
                </a>
            </li>
            
            <!-- lookups_management end -->

            <li class="navigation-header">
                <span>{{tr('setting_management')}}</span>
            </li>

            <li class="nav-item" id="profile">
                <a href="{{route('support_member.profile')}}">
                    <i class="fa fa-user"></i>
                    <span class="menu-title" data-i18n="">{{tr('account')}}</span>
                </a>
            </li>
        
            <li class="nav-item">
                <a data-toggle="modal" data-target="#logoutModel" href="{{route('support_member.logout')}}" onclick="return confirm('Are You sure?')">
                    <i class="ft-power"></i>
                    <span class="menu-title" data-i18n="">{{tr('logout')}}</span>
                </a>
            </li>

        </ul>
    </div>
</div>
