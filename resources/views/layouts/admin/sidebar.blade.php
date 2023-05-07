  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
		 <div class="ulogo">
			 <a href="index.html">
			  <!-- logo for regular state and mobile devices -->
			  <span><b> </b>{{Auth::guard('admin')->user()->name}}</span>
			</a>
		</div>
        <div class="image">
          <img src="{{Auth::guard('admin')->user()->picture}}" class="rounded-circle" alt="User Image">
        </div>
        <div class="info">
			<a href="{{route('admin.settings')}}" class="link" data-toggle="tooltip" title="" data-original-title="Settings"><i class="ion ion-gear-b"></i></a>
            <a href="{{route('admin.profile')}}" class="link" data-toggle="tooltip" title="" data-original-title="Profile"><i class="ion ion-person"></i></a>
            <a onclick="return confirm(&quot;{{tr('confirm_logout')}}&quot;);" href="{{route('admin.logout')}}" class="link" data-toggle="tooltip" title="" data-original-title="Logout"><i class="ion ion-power"></i></a>
        </div>
      </div>
      <!-- sidebar menu -->
      <ul class="sidebar-menu" data-widget="tree">
		<li class="nav-devider"></li>
        <li >
          <a href="{{route('admin.dashboard')}}">
            <i class="fa fa-tachometer-alt"></i> <span>{{tr('dashboard')}}</span>
          
          </a>
        </li>
        <li class="nav-devider"></li>
        <li class="header nav-small-cap">{{tr('account_management')}}</li>
        <li class="treeview" id="users">
          <a href="{{route('admin.users.index')}}">
            <i class="fa fa-users"></i>
            <span>{{tr('users')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="users-create"><a href="{{route('admin.users.create')}}">{{tr('add_user')}}</a></li>
            <li id="users-view"><a href="{{route('admin.users.index')}}">{{tr('view_users')}}</a></li>
            @if(Setting::get('is_referral_enabled') == YES)
                <li id="user_referrals"><a href="{{route('admin.referrals.index')}}">{{tr('user_referrals')}}</a></li>
            @endif
          </ul>
        </li>
       
        <li id="users-documents">
          <a href="{{route('admin.user_documents.index')}}">
            <i class="fa fa-shield-alt"></i> <span>{{tr('verification_documents')}}</span>
          </a>
        </li>

        <li id="free-users">
          <a href="{{route('admin.users.index', ['account_type' => USER_FREE_ACCOUNT])}}">
            <i class="fa fa-users"></i> <span>{{tr('free_users')}}</span>
          </a>
        </li>

        <li id="premium-users">
          <a href="{{route('admin.users.index', ['account_type' => USER_PREMIUM_ACCOUNT])}}">
            <i class="fa fa-users"></i> <span>{{tr('premium_users')}}</span>
           
          </a>
        </li>

        <li class="nav-devider"></li>

        <li class="header nav-small-cap">{{tr('post_management')}}</li>
		    <li class="treeview" id="posts">
          <a href="{{route('admin.posts.index')}}">
            <i class="fa fa-image"></i>
            <span>{{tr('posts')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="posts-create"><a href="{{route('admin.posts.create')}}">{{tr('create_post')}}</a></li>
            <li id="posts-view"><a href="{{route('admin.posts.index')}}"> {{tr('view_posts')}}</a></li>
            <li id="report-posts"><a href="{{route('admin.report_posts.index')}}"> {{tr('reported_posts')}}</a></li>
          </ul>
        </li>

        <li class="treeview" id="categories">
          <a href="{{route('admin.categories.index')}}">
            <i class="fa fa-list"></i>
            <span>{{tr('categories')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="categories-create"><a href="{{route('admin.categories.create')}}">{{tr('add_category')}}</a></li>
            <li id="categories-view"><a href="{{route('admin.categories.index')}}"> {{tr('view_category')}}</a></li>
          </ul>
        </li>

        <li class="treeview" id="hashtags">
          <a href="{{route('admin.hashtags.index')}}">
            <i class="fa fa-hashtag"></i>
            <span>{{tr('hashtags')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="hashtags-create"><a href="{{route('admin.hashtags.create')}}">{{tr('add_hashtag')}}</a></li>
            <li id="hashtags-view"><a href="{{route('admin.hashtags.index')}}"> {{tr('view_hashtags')}}</a></li>
          </ul>
        </li>

        <li class="treeview" id="stories">
          <a href="{{route('admin.stories.index')}}">
            <i class="fa fa-image"></i>
            <span>{{tr('stories')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="stories-create"><a href="{{route('admin.stories.create')}}">{{tr('create_story')}}</a></li>
            <li id="stories-view"><a href="{{route('admin.stories.index')}}"> {{tr('view_stories')}}</a></li>
          </ul>
        </li>

        <!-- <li class="treeview" id="vods">
          <a href="{{route('admin.vod_videos.index')}}">
            <i class="fa fa-image"></i>
            <span>{{tr('vods')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="vods-create"><a href="{{route('admin.vod_videos.create')}}">{{tr('create_vods')}}</a></li>
            <li id="vods-view"><a href="{{route('admin.vod_videos.index')}}"> {{tr('view_vods')}}</a></li>
          </ul>
        </li> -->

        <li class="header nav-small-cap">{{tr('products_management')}}</li>

        <li class="treeview" id="user_products">
          <a href="{{route('admin.user_products.index')}}">
            <i class="fa fa-shopping-cart"></i>
            <span>{{tr('user_products')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="user_products-create"><a href="{{route('admin.user_products.create')}}">{{tr('add_user_product')}}</a></li>
            <li id="user_products-view"><a href="{{route('admin.user_products.index')}}"> {{tr('view_user_products')}}</a></li>
          </ul>
        </li>

        <li class="treeview" id="product_categories">
          <a href="{{route('admin.product_categories.index')}}">
            <i class="fa fa-list"></i>
            <span>{{tr('product_categories')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="product_categories-create"><a href="{{route('admin.product_categories.create')}}">{{tr('add_product_category')}}</a></li>
            <li id="product_categories-view"><a href="{{route('admin.product_categories.index')}}"> {{tr('view_product_categories')}}</a></li>
          </ul>
        </li>

        <li class="treeview" id="product_sub_categories">
          <a href="{{route('admin.product_sub_categories.index')}}">
            <i class="fa fa-list"></i>
            <span>{{tr('product_sub_categories')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="product_sub_categories-create"><a href="{{route('admin.product_sub_categories.create')}}">{{tr('add_product_sub_category')}}</a></li>
            <li id="product_sub_categories-view"><a href="{{route('admin.product_sub_categories.index')}}"> {{tr('view_product_sub_categories')}}</a></li>
          </ul>
        </li>

        <!-- <li class="treeview" id="promo_codes">

            <a href="{{route('admin.promo_codes.index')}}">
                <i class="menu-icon fa fa-globe"></i>
                <span>{{ tr('promo_codes') }}</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-right pull-right"></i>
                </span>
            </a>
              <ul class="treeview-menu">
                    <li id="promo_codes-create"> 
                        <a class="nav-link" href="{{route('admin.promo_codes.create')}} "> {{ tr('add_promo_code') }} </a>
                    </li>

                    <li id="promo_codes-view"> 
                        <a class="nav-link" href="{{route('admin.promo_codes.index')}} "> {{ tr('view_promo_codes') }}  </a>
                    </li>
              </ul>

        </li> -->



        @if(Setting::get('is_one_to_one_call_enabled') || Setting::get('is_one_to_many_call_enabled'))

            <li class="header nav-small-cap">{{tr('video_management')}}</li>

            @if(Setting::get('is_one_to_many_call_enabled'))

                <li class="treeview" id="live-videos">
                  <a href="{{route('admin.live_videos.index')}}">
                    <i class="fa fa-video"></i>
                    <span>{{tr('live_videos')}}</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li id="live-videos-live"><a href="{{route('admin.live_videos.onlive')}}">{{tr('on_live')}}</a></li>
                    <li id="live-videos-history"><a href="{{route('admin.live_videos.index')}}"> {{tr('history')}}</a></li>
                  </ul>
                </li>

            @endif

            @if(Setting::get('is_one_to_one_call_enabled'))

                <li class="treeview" id="one-to-one">
                  <a href="{{route('admin.audio_call_requests.index')}}">
                    <i class="fa fa-phone"></i>
                    <span>{{tr('video_calls')}}</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-right pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li id="video-call-requests"><a href="{{route('admin.video_call_requests.index')}}">{{tr('video_call_requests')}}</a></li>
                    <li id="audio-call-requests"><a href="{{route('admin.audio_call_requests.index')}}"> {{tr('audio_call_requests')}}</a></li>
                  </ul>
                </li>
                <!-- <li id="video_call_requests">
                  <a href="{{route('admin.video_call_requests.index')}}">
                    <i class="fa fa-video-camera"></i> <span>{{tr('video_calls')}}</span>
                  
                  </a>
                </li> -->
            @endif

        @endif
       


        <li class="header nav-small-cap">{{tr('revenue_management')}}</li>
        <li id="revenue-dashboard">
          <a href="{{route('admin.revenues.dashboard')}}">
            <i class="fa fa-tachometer-alt"></i> <span>{{tr('revenue_dashboard')}}</span>
           
          </a>
        </li>
        <li class="treeview" id="payments">
          <a href="#">
            <i class="fa fa-money"></i> <span>{{tr('payments')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="post-payments"><a href="{{route('admin.post.payments')}}">{{tr('post_payments')}}</a></li>
            <li id="tip-payments"><a href="{{route('admin.user_tips.index')}}">{{tr('tip_payments')}}</a></li>
            <li id="user-subscription-payments"><a href="{{route('admin.users_subscriptions.index')}}">{{tr('subscription_payments')}}</a></li>
            @if(Setting::get('is_one_to_many_call_enabled'))
                <li id="live-video-payments">
                    <a href="{{route('admin.live_videos.payments')}}">
                       {{tr('live_video_payments')}}
                    </a>
                </li>
            @endif
                
            @if(Setting::get('is_one_to_one_call_enabled'))
                <li id="video-call-payments">
                    <a href="{{route('admin.video_call_payments.index')}}">
                       {{tr('video_call_payments')}}
                    </a>
                </li>

                <li id="audio-call-payments">
                    <a href="{{route('admin.audio_call_payments.index')}}">
                       {{tr('audio_call_payments')}}
                    </a>
                </li>
            @endif
            <li id="chat-asset-payments"><a href="{{route('admin.chat_asset_payments.index')}}">{{tr('chat_asset_payments')}}</a></li>
             <li id="order-payments"><a href="{{route('admin.order.payments')}}">{{tr('order_payments')}}</a></li>
          </ul>
        </li>
            
        <li id="user_wallets">
          <a href="{{route('admin.user_wallets.index')}}">
            <i class="fa fa-wallet"></i> <span>{{tr('user_wallets')}}</span>
           
          </a>
        </li>

        <li id="content_creator-withdrawals">
          <a href="{{route('admin.user_withdrawals')}}">
            <i class="fa fa-location-arrow"></i> <span>{{tr('user_withdrawals')}}</span>
          </a>
        </li>


        <li class="header nav-small-cap">{{tr('lookups_management')}}</li>

        <li class="treeview" id="documents">
          <a href="#">
            <i class="fa fa-file"></i> <span>{{tr('documents')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="documents-create"><a href="{{route('admin.documents.create')}}">{{tr('add_document')}}</a></li>
            <li id="documents-view"><a href="{{route('admin.documents.index')}}">{{tr('view_documents')}}</a></li>
          </ul>
        </li>

        <li class="treeview" id="static_pages">
          <a href="#">
            <i class="fa fa-file"></i> <span>{{tr('static_pages')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="static_pages-create"><a href="{{route('admin.static_pages.create')}}">{{tr('add_static_page')}}</a></li>
            <li id="static_pages-view"><a href="{{route('admin.static_pages.index')}}">{{tr('view_static_pages')}}</a></li>
          </ul>
        </li>

        <li class="treeview" id="report_reasons">
          <a href="#">
            <i class="fa fa-file"></i> <span>{{tr('report_reasons')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="report_reasons-create"><a href="{{route('admin.report_reasons.create')}}">{{tr('add_report_reason')}}</a></li>
            <li id="report_reasons-view"><a href="{{route('admin.report_reasons.index')}}">{{tr('view_report_reasons')}}</a></li>
          </ul>
        </li>


        <li class="header nav-small-cap">{{tr('setting_management')}}</li>

        <li id="settings">
          <a href="{{route('admin.settings')}}">
            <i class="fa fa-cog"></i> <span>{{tr('settings')}}</span>
           
          </a>
        </li>
        <li id="profile">
          <a href="{{route('admin.profile')}}">
            <i class="fa fa-user"></i> <span>{{tr('account')}}</span>
           
          </a>
        </li>

        <li>
          <a onclick="return confirm(&quot;{{tr('confirm_logout')}}&quot;);" href="{{route('admin.logout')}}">
            <i class="fa fa-power-off"></i> <span>{{tr('logout')}}</span>
           
          </a>
        </li>
        
      </ul>
    </section>
  </aside>