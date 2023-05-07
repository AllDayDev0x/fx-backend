<div class="dropdown">
    <button class="btn dropdown-toggle btn-warning" type="button" data-toggle="dropdown">{{tr('action')}}</button>
<!--     <div class="dropdown-menu action-dropdown-menu dropdown-grid cols-4"> -->
    <div class="dropdown-menu dropdown-grid cols-4 action-dropdown-menu">

        {{-- Users CRUD Actions start --}}

        <a class="dropdown-item" href="{{route('admin.users.view', ['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/view_details.svg')}}" alt="view"/>
            <span class="title">{{tr('view')}}</span>
        </a>

        @if(Setting::get('is_demo_control_enabled') == YES)

            <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

            <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

        @else 

            <a class="dropdown-item" href="{{route('admin.users.edit', ['user_id' => $user->id])}}">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/edit_image.svg')}}" alt="about.svg"/>
                <span class="title">{{tr('edit')}}</span>
            </a>
            
            <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('user_delete_confirmation' , $user->name) }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user->id,'page'=>request()->input('page')] ) }}">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/full_trash.svg')}}" alt="full_trash.svg"/>
                <span class="title">{{tr('delete')}}</span>
            </a>

        @endif

        {{-- Users CRUD Actions end --}}

        {{-- User Approve/Decline actions start --}}

        @if($user->status == APPROVED)

            <a class="dropdown-item" href="{{route('admin.users.status', ['user_id' => $user->id])}}" onclick="return confirm(&quot;{{ $user->name }} - {{ tr('user_decline_confirmation') }}&quot;);">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/disapprove.svg')}}" alt="disapprove.svg"/>
                <span class="title">{{tr('decline')}}</span>
            </a>

        @else

            <a class="dropdown-item" href="{{route('admin.users.status', ['user_id' => $user->id])}}" >
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/approve.svg')}}" alt="approve.svg"/>
                <span class="title">{{tr('approve')}}</span>
            </a>

        @endif

        {{-- User Approve/Decline actions end --}}

        {{-- Upgrade Premium Account Start --}}

        <a class="dropdown-item" href="{{route('admin.users.view', ['user_id' => $user->id])}}" data-toggle="modal" data-target="#{{$user->id}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/approval.svg')}}" alt="approval.svg"/>
            <span class="title">{{($user->user_account_type  == USER_FREE_ACCOUNT) ? tr('upgrade_to_premium') : tr('update_premium')}}</span>
        </a>

        {{-- Upgrade Premium Account End --}}


        {{-- Verify badge start --}}

        @if(Setting::get('is_verified_badge_enabled'))

            @if($user->is_verified_badge == YES)

                <a class="dropdown-item" href="{{route('admin.users.verify_badge' , ['user_id' => $user->id] )  }}">
                    <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/dislike.svg')}}" alt="dislike.svg"/>
                    <span class="title">{{tr('remove_badge')}}</span>
                </a>

            @else

                <a class="dropdown-item" href="{{route('admin.users.verify_badge' , ['user_id' => $user->id])}}">
                    <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/like.svg')}}" alt="like.svg"/>
                    <span class="title">{{tr('add_badge')}}</span>
                </a>
        
            @endif

        @endif

        {{-- Verify badge end --}}

        {{-- Content Creators actions start --}}

        <a class="dropdown-item" href="{{route('admin.user_documents.view', ['user_id'=>$user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/folder.svg')}}" alt="folder.svg"/>
            <span class="title">{{tr('documents')}}</span>
        </a>

        @if($user->is_content_creator == CONTENT_CREATOR)

            <a class="dropdown-item" href="{{route('admin.users.report_dashboard', ['user_id' => $user->id])}}">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/line_chart.svg')}}" alt="line_chart.svg"/>
                <span class="title">{{tr('reports')}}</span>
            </a>

            <a class="dropdown-item" href="{{route('admin.users.dashboard', ['user_id' => $user->id])}}">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/bullish.svg')}}" alt="bullish.svg"/>
                <span class="title">{{tr('dashboard')}}</span>
            </a>

            <a class="dropdown-item" href="{{route('admin.posts.index', ['user_id' => $user->id])}}">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/camera.svg')}}" alt="camera.svg"/>
                <span class="title">{{tr('posts')}}</span>
            </a>

        @else

            <a onclick="return confirm(&quot;{{ tr('upgrade_to_content_creator_confirmation' , $user->name) }}&quot;);" class="dropdown-item" href="{{ route('admin.users.content_creator_upgrade', ['user_id' => $user->id] ) }}">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/photo_reel.svg')}}" alt="photo_reel.svg"/>
                <span class="title">{{ tr('upgrade_to_content_creator') }}</span>
            </a>

        @endif

        {{-- Content Creators actions end --}}

        {{-- Follow actions start --}}

        <a class="dropdown-item" href="{{route('admin.user_followings', ['following_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/followings.svg')}}" alt="followings.svg"/>
            <span class="title">{{tr('followings')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.user_followers', ['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/instagram-followers.svg')}}" alt="instagram-followers.svg"/>
            <span class="title">{{tr('followers')}}</span>
        </a>

        {{-- Follow actions end --}}

        {{-- Wallet Section Start --}}

        <a class="dropdown-item" href="{{route('admin.user_wallets.view', ['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/wallet.svg')}}" alt="wallet.svg"/>
            <span class="title">{{tr('wallet')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.user_withdrawals', ['user_id'=>$user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/withdraw.svg')}}" alt="withdraw.svg"/>
            <span class="title">{{tr('withdrawal')}}</span>
        </a>

        <a class="dropdown-item" href="{{ route('admin.users.billing_accounts', ['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/business.svg')}}" alt="business.svg"/>
            <span class="title">{{tr('billing_accounts')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.block_users.index', ['user_id'=>$user->id] )}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/cancel.svg')}}" alt="cancel.svg"/>
            <span class="title">{{tr('blocked_users')}}</span>
        </a>

        {{-- Wallet Section End --}}

        {{-- Fav's List start --}}

        <a class="dropdown-item" href="{{route('admin.bookmarks.index', ['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/bookmarks.svg')}}" alt="bookmarks.svg"/>
            <span class="title">{{tr('bookmarks')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.fav_users.index', ['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/favorite.svg')}}" alt="favorite.svg"/>
            <span class="title">{{tr('favorite_users')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.post_likes.index', ['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/likes.svg')}}" alt="likes.svg"/>
            <span class="title">{{tr('liked_posts')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.stories.index', ['user_id'=>$user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/story.svg')}}" alt="story.svg"/>
            <span class="title">{{tr('stories')}}</span>
        </a>

        {{-- Fav's List end --}}

        {{-- User Sessions start --}}

        <a class="dropdown-item" href="{{route('admin.user_login_session.index', ['user_id'=>$user->id] )}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/login-session.svg')}}" alt="login-session.svg"/>
            <span class="title">{{tr('user_sessions')}}</span>
        </a>

        @if($user->is_content_creator == CONTENT_CREATOR)

            {{-- <a class="dropdown-item" href="{{ route('admin.promo_codes.index', ['user_id' => $user->id] ) }}">{{ tr('promo_codes') }}</a> --}}

            <a class="dropdown-item" href="{{route('admin.user_products.index', ['user_id' => $user->id] )}}">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/products.svg')}}" alt="products.svg"/>
                <span class="title">{{tr('products')}}</span>
            </a>

        @endif

        <a class="dropdown-item" href="{{route('admin.orders.index', ['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/order.svg')}}" alt="order.svg"/>
            <span class="title">{{tr('orders')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.users.carts', ['user_id'=>$user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/cart.svg')}}" alt="cart.svg"/>
            <span class="title">{{tr('cart')}}</span>
        </a>

        {{-- <a class="dropdown-item" href="{{route('admin.delivery_address.index', ['user_id'=>$user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/instagram-followers.svg')}}" alt="instagram-followers.svg"/>
            <span class="title">{{tr('delivery_address')}}</span>
        </a> --}}

        {{-- User Sessions end --}}

        {{-- Payments starts --}}

        <a class="dropdown-item" href="{{route('admin.users_subscriptions.index',['from_user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/subscription-payments.svg')}}" alt="subscription-payments.svg"/>
            <span class="title">{{tr('subscription_payments')}}</span>
        </a>

        @if($user->user_account_type == USER_PREMIUM_ACCOUNT)

        <a class="dropdown-item" href="{{route('admin.post.payments',['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/inspection.svg')}}" alt="inspection.svg"/>
            <span class="title">{{tr('post_payments')}}</span>
        </a>

        @endif

        <a class="dropdown-item" href="{{route('admin.user_tips.index',['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/tip-payments.svg')}}" alt="tip-payments.svg"/>
            <span class="title">{{tr('tip_payments')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.live_videos.payments',['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/broadcast.svg')}}" alt="broadcast.svg"/>
            <span class="title">{{tr('live_video_payments')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.video_call_payments.index',['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/video.svg')}}" alt="video.svg"/>
            <span class="title">{{tr('video_call_payments')}}</span>
        </a>

        <a class="dropdown-item" href="{{route('admin.audio_call_payments.index',['user_id' => $user->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/audio.svg')}}" alt="audio.svg"/>
            <span class="title">{{tr('audio_call_payments')}}</span>
        </a>

        {{-- Payments ends --}}

    </div>
</div>