@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')


<li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('users')}}</a>
</li>
<li class="breadcrumb-item active">{{tr('view_user')}}</a>
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-xl-12 col-lg-12">

            <div class="card user-profile-view-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('view_users')}}</h4>

                </div>

                <div class="card-content">

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">

                                <div class="card profile-with-cover">

                                    &emsp;&emsp;{{tr('profile')}}

                                    <div class="media profil-cover-details w-100">

                                        <div class="media-left pl-2 pt-2">

                                            <a class="profile-image">
                                                <img src="{{ $user->picture}}" alt="{{ $user->name}}" class="img-thumbnail img-fluid img-border height-100" alt="Card image">
                                            </a>

                                        </div>
                                       
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="col-md-3">

                                <div class="card profile-with-cover">

                                    &emsp;&emsp;{{tr('cover')}}

                                    <div class="media profil-cover-details w-100">

                                        <div class="media-left pl-2 pt-2">

                                            <a class="profile-image">
                                                <img src="{{ $user->cover}}" alt="{{ $user->name}}" class="img-thumbnail img-fluid img-border height-100" alt="Card image">
                                            </a>

                                        </div>
                                       
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="col-md-6">
                                <br>
                                <div class="row">
                                    @if(Setting::get('is_demo_control_enabled') == YES)
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-social btn-dropbox mr-1 mb-1" href="javascript:void(0)">
                                            <i class="fa fa-edit"></i>{{tr('edit')}}
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-social btn-twitter mr-1 mb-1" href="javascript:void(0)">
                                            <i class="fa fa-delete"></i>
                                            {{tr('delete')}}
                                        </a>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-social btn-dropbox mr-1 mb-1 " href="{{route('admin.users.edit', ['user_id'=>$user->id] )}}">
                                            <i class="fa fa-edit"></i>
                                            {{tr('edit')}}
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-social btn-google mr-1 mb-1" onclick="return confirm(&quot;{{tr('user_delete_confirmation' , $user->name)}}&quot;);" href="{{route('admin.users.delete', ['user_id'=> $user->id] )}}">
                                            <i class="fa fa-trash"></i>
                                            {{tr('delete')}}
                                        </a>
                                    </div>
                                    @endif

                                </div>  
                                
                                <div class="row">
                                     
                                     <div class="col-md-6">
                                         @if($user->status == APPROVED)
                                            <a class="btn btn-block btn-social btn-foursquare mr-1 mb-1" href="{{route('admin.users.status' ,['user_id'=> $user->id] )}}" onclick="return confirm(&quot;{{$user->name}} - {{tr('user_decline_confirmation')}}&quot;);">
                                                <i class="fa fa-user-times"></i>
                                                {{tr('decline')}}
                                            </a>
                                        @else

                                            <a class="btn btn-block btn-social btn-twitter mr-1 mb-1" href="{{route('admin.users.status' , ['user_id'=> $user->id] )}}">
                                                <i class="fa fa-user-check"></i>
                                                {{tr('approve')}}
                                            </a>
                                        @endif
                                    
                                    </div>
                                    <div class="col-md-6">
                                        <a href="{{route('admin.user_documents.view', array('user_id'=>$user->id))}}" class="btn btn-block btn-social btn-twitter mr-1 mb-1">
                                            <i class="fa fa-file"></i>
                                            {{tr('documents')}}
                                        </a>
                                    </div>
                                </div>   
                            </div>
                        </div>
                       
                    </div>

                    <hr>
                    <div class="user-view-padding">
                        <div class="row"> 

                            <div class=" col-xl-6 col-lg-6 col-md-12">
                                <div class="table-responsive">

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                        <tr >
                                            <th style="border-top: 0">{{tr('username')}}</th>
                                            <td style="border-top: 0">{{$user->username}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('email')}}</th>
                                            <td>{{$user->email}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('timezone')}}</th>
                                            <td>{{$user->timezone}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('login_type')}}</th>
                                            <td class="text-capitalize">{{$user->login_by ?: tr('not_available')}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('device_type')}}</th>
                                            <td class="text-capitalize">{{$user->device_type ?: tr('not_available')}}</td>
                                        </tr>

                                        @if(Setting::get('is_verified_badge_enabled'))
                                        <tr>
                                        
                                            <th>{{tr('is_badge_verified')}}</th>
                                            <td>
                                                @if($user->is_verified_badge == YES)

                                                <span class="badge badge-success">{{tr('yes')}}</span>

                                                @else
                                                <span class="badge badge-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                            
                                        </tr>
                                        @endif
                                        
                                        <tr>
                                            <th>{{tr('is_email_verified')}}</th>
                                            <td>
                                                @if($user->is_email_verified == YES)

                                                <span class="badge badge-success">{{tr('yes')}}</span>

                                                @else
                                                <span class="badge badge-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('status')}}</th>
                                            <td>
                                                @if($user->status == USER_APPROVED)

                                                <span class="badge badge-success">{{tr('approved')}}</span>

                                                @else
                                                <span class="badge badge-danger">{{tr('declined')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('account_type')}}</th>
                                            <td>
                                                @if($user->user_account_type == USER_PREMIUM_ACCOUNT)

                                                <span class="badge badge-success">{{tr('premium_users')}}</span>

                                                @else
                                                <span class="badge badge-danger">{{tr('free_users')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('is_content_creator')}} ?</th>
                                            <td>
                                                @if($user->is_content_creator == CONTENT_CREATOR)
                                        
                                                <span class="badge badge-success">{{ tr('yes') }}</span>

                                                @else

                                                <span class="badge badge-primary">{{ tr('no') }}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        @if($user->user_account_type == USER_PREMIUM_ACCOUNT)

                                            @if(Setting::get('is_only_wallet_payment'))

                                            <tr>
                                                <th>{{tr('monthly_token')}}</th>
                                                <td>
                                                    {{($user->userSubscription) ? formatted_amount($user->userSubscription->monthly_token) : '-'}}
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('yearly_token')}}</th>
                                                <td>
                                                    {{($user->userSubscription) ? formatted_amount($user->userSubscription->yearly_token) : '-'}}
                                                </td>
                                            </tr>

                                            @else
                                            
                                            <tr>
                                                <th>{{tr('monthly_amount')}}</th>
                                                <td>
                                                    {{($user->userSubscription) ? formatted_amount($user->userSubscription->monthly_amount) : '-'}}
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('yearly_amount')}}</th>
                                                <td>
                                                    {{($user->userSubscription) ? formatted_amount($user->userSubscription->yearly_amount) : '-'}}
                                                </td>
                                            </tr>

                                            @endif

                                        @endif 

                                        <tr>
                                            <th>{{tr('mobile')}}</th>
                                            <td>{{$user->mobile ?: tr('not_available')}}</td>
                                        </tr>

                                         <tr>
                                            <th>{{tr('categories')}}</th>
                                            <td>{{$categories->pluck('name')->implode(', ') ?: tr('n_a')}}</td>   
                                        </tr>

                                        <tr>
                                            <th>{{tr('user_wallet_balance')}}</th>
                                            <td>
                                                {{$user->userWallets->remaining_formatted ?? formatted_amount(0.00)}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('tipped_amount')}}</th>
                                            <td>
                                                <a href="{{route('admin.user_tips.index',['user_id'=>$user->id])}}">
                                                {{$user->tipped_amount ?? 0.00}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('post_amount')}}</th>
                                            <td>
                                                <a href="{{route('admin.post.payments',['user_id'=>$user->id])}}">
                                                {{$user->total_post_payment ?? 0.00}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('total_followers')}}</th>
                                            <td>
                                                
                                                {{$user->total_followers ?? 0}}
                                                
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('total_following')}}</th>
                                            <td>
                                                {{$user->total_followings ?? 0}}
                                            
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('total_fav')}}</th>
                                            <td>
                                                {{$user->total_fav_users ?? 0}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('email_notification')}}</th>
                                            <td>
                                                @if($user->is_email_notification == YES)

                                                <span class="badge badge-success">{{tr('on')}}</span>

                                                @else
                                                <span class="badge badge-danger">{{tr('off')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('push_notification')}}</th>
                                            <td>
                                                @if($user->is_push_notification == YES && $user->mobile !='')

                                                <span class="badge badge-success">{{tr('on')}}</span>

                                                @else
                                                <span class="badge badge-danger">{{tr('off')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('two_step_auth')}}</th>

                                            <td>
                                                @if($user->is_two_step_auth_enabled == TWO_STEP_AUTH_DISABLE)

                                                <span class="badge badge-danger">{{ tr('off') }}</span>

                                                @else

                                                <span class="badge badge-success">{{ tr('on') }}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('created_at')}} </th>
                                            <td>{{common_date($user->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('updated_at')}} </th>
                                            <td>{{common_date($user->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                    </table>
                                    @if(Setting::get('is_referral_enabled') == YES)
                                    <br><br>
                                    <hr>
                                    <div class="card-title"><h4><b>{{tr('referrals')}}</b></h4></div>
                                    
                                    <table class="table table-xl mb-0">
                                       
                                        <tr>
                                            <th>{{tr('referral')}}</th>
                                            <td>{{$referral_code->referral_code }}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('total_referrals')}}</th>

                                            <td>
                                                <a href="{{ route('admin.users.index',['referral_code_id' => $referral_code->referral_code_id])}}">
                                                    {{$referral_code->total_referrals ?? '-'}}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('referral_earning')}}</th>
                                            <td>{{$referral_code->referral_earnings_formatted}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('referee_earnings')}}</th>
                                            <td>{{$referral_code->referee_earnings_formatted}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('total_earnings')}}</th>
                                            <td>{{$referral_code->total_formatted}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('used')}}</th>
                                            <td>{{$user->userWallets ? (formatted_amount($referral_code->total_earnings - $user->userWallets->referral_amount)) : formatted_amount(0.00)}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('remaining')}}</th>
                                            <td>{{$referral_code->remaining_formatted ?? formatted_amount(0.00)}}</td>
                                        </tr>
                                      
                                    </table>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-12">

                                <div class="px-2 resp-marg-top-xs">

                                    <div class="card-title mb-4"><h4>{{tr('action')}}</h4></div>

                                    <div class="row">

                                       

                                        <div class="col-xl-4 col-lg-4 col-md-12" style="display: none;">

                                            <a href="{{route('admin.delivery_address.index',['user_id' => $user->id])}}" class="btn btn-block btn-social btn-dropbox mr-1 mb-2">
                                                <i class="fa fa-address-book"></i>
                                                {{tr('delivery_address')}}
                                            </a>

                                        </div>

                                    
                                        @if($user->is_content_creator == CONTENT_CREATOR)
                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a href="{{route('admin.post.payments',['user_id' => $user->id])}}" class="btn btn-block btn-social btn-facebook mr-1 mb-2">
                                                <i class="fa fa-image"></i>
                                                {{tr('post_payments')}}
                                            </a>

                                        </div>
                                        @else

                                            <div class="col-xl-4 col-lg-4 col-md-12">

                                                <a onclick="return confirm(&quot;{{ tr('upgrade_to_content_creator_confirmation' , $user->name) }}&quot;);" href="{{route('admin.users.content_creator_upgrade',['user_id' => $user->id])}}" class="btn btn-block btn-social btn-foursquare mr-1 mb-2">
                                                    <i class="fa fa-arrow-up"></i>
                                                    {{tr('upgrade_to_content_creator')}}
                                                </a>

                                            </div>

                                        @endif

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                        <a href="{{route('admin.users_subscriptions.index',['from_user_id' => $user->id])}}" class="btn btn-block btn-social btn-flickr mr-1 mb-2">
                                            <i class="fa fa-user-plus"></i>
                                            {{tr('subscription_payments')}}
                                        </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                        <a href="{{route('admin.user_tips.index',['user_id' => $user->id])}}" class="btn btn-block btn-social btn-bitbucket mr-1 mb-2">
                                            <i class="fa fa-money"></i>
                                            {{tr('tip_payments')}}</a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a href="{{route('admin.orders.index',['user_id' => $user->id])}}" class="btn btn-block btn-social btn-github mr-1 mb-2">
                                                <i class="fa fa-mobile"></i>
                                                {{tr('orders')}}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-google mr-1 mb-2" href="{{route('admin.live_videos.payments',['user_id' => $user->id])}}">
                                                <i class="fa fa-podcast"></i>
                                                {{tr('live_video_payments')}}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">
                                           
                                            <a class="btn btn-block btn-social btn-linkedin mr-1 mb-2" href="{{route('admin.video_call_payments.index',['user_id' => $user->id])}}">
                                                <i class="fa fa-file-video-o"></i>
                                                {{tr('video_call_payments')}}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">
                                           
                                            <a class="btn btn-block btn-social btn-instagram mr-1 mb-2" href="{{route('admin.audio_call_payments.index',['user_id' => $user->id])}}">
                                                <i class="fa fa-file-audio-o"></i>
                                                {{tr('audio_call_payments')}}
                                            </a>

                                        </div>
                                    
                                        @if($user->is_content_creator == CONTENT_CREATOR)
                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-tumblr mr-1 mb-2" href="{{ route('admin.posts.index', ['user_id' => $user->id] ) }}">
                                                <i class="fa fa-th"></i>
                                                {{ tr('posts') }}
                                            </a>

                                        </div>
                                        @endif

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a href="{{ route('admin.bookmarks.index', ['user_id' => $user->id] ) }}" class="btn btn-block btn-social btn-twitter mr-1 mb-2">
                                                {{tr('bookmarks')}}
                                                <i class="fa fa-bookmark"></i>
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a href="{{ route('admin.fav_users.index', ['user_id' => $user->id] ) }}" class="btn btn-block btn-social btn-vk mr-1 mb-2">
                                                <i class="fa fa-star"></i>
                                                {{tr('favorite_users')}}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-dropbox mr-1 mb-2" href="{{ route('admin.post_likes.index', ['user_id' => $user->id] ) }}">
                                                <i class="fa fa-thumbs-up"></i>
                                                {{ tr('liked_posts') }}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-bitbucket mr-1 mb-2" href="{{ route('admin.user_wallets.view', ['user_id' => $user->id] ) }}">
                                                <i class="fas fa-wallet"></i>
                                                {{ tr('wallet') }}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-twitter mr-1 mb-2" href="{{ route('admin.user_withdrawals', ['user_id' => $user->id] ) }}">
                                                <i class="fa fa-location-arrow"></i>
                                                {{ tr('withdrawal') }}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-flickr mr-1 mb-2" href="{{ route('admin.users.view', ['user_id' => $user->id] ) }}" data-toggle="modal" data-target="#{{$user->id}}">
                                                <i class="fa fa-check"></i>
                                                    {{ ($user->user_account_type  == USER_FREE_ACCOUNT) ? tr('upgrade_to_premium') : tr('update_premium') }}
                                                </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-github mr-1 mb-2 " href="{{route('admin.users.billing_accounts', ['user_id'=>$user->id] )}}">
                                                <i class="fa fa-credit-card"></i>
                                                {{tr('billing_accounts')}}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a href="{{route('admin.user_followings',['following_id' => $user->id])}}" class="btn btn-block btn-social btn-dropbox mr-1 mb-2">
                                                <i class="fa fa-heart"></i>
                                                {{tr('followings')}}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a href="{{route('admin.user_followers', ['user_id' => $user->id])}}" class="btn btn-block btn-social btn-linkedin mr-1 mb-2">
                                                <i class="fa fa-inbox"></i>
                                                {{tr('followers')}}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-vk mr-1 mb-2" href="{{route('admin.users.carts', ['user_id'=>$user->id] )}}">
                                                <i class="fa fa-shopping-cart"></i>
                                                {{tr('cart')}}
                                            </a>

                                        </div>

                                        @if($user->is_content_creator == CONTENT_CREATOR)

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-google mr-1 mb-2" href="{{ route('admin.user_products.index', ['user_id' => $user->id] ) }}">
                                                <i class="fa fa-shipping-fast"></i>
                                                {{tr('product')}}
                                            </a>

                                        </div>
                                        @endif

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-foursquare mr-1 mb-2" href="{{route('admin.block_users.index', ['user_id'=>$user->id] )}}">
                                                <i class="fa fa-user-times"></i>
                                                {{tr('blocked_users')}}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                            <a class="btn btn-block btn-social btn-twitter mr-1 mb-2" href="{{route('admin.user_login_session.index', ['user_id'=>$user->id])}}">
                                                <i class="fa fa-globe"></i>
                                                {{tr('user_sessions')}}
                                            </a>

                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-12">

                                        <a href="{{route('admin.stories.index',['user_id' => $user->id])}}" class="btn btn-block btn-social btn-bitbucket mr-1 mb-2">
                                            <i class="fa fa-history"></i>
                                            {{tr('story')}}</a>

                                        </div>

                                    </div>

                                </div>

                                <div class="table-responsive">

                                    <hr>

                                    <h4>{{tr('social_settings')}}</h4>
                                    <hr>

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                        <tr >
                                            <th style="border-top: 0">{{tr('amazon_wishlist')}}</th>
                                            <td style="border-top: 0">{{$user->amazon_wishlist ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('website')}}</th>
                                            <td>{{$user->website ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('instagram_link')}}</th>
                                            <td>{{$user->instagram_link ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('facebook_link')}}</th>
                                            <td>{{$user->facebook_link ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('twitter_link')}}</th>
                                            <td>{{$user->twitter_link ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('linkedin_link')}}</th>
                                            <td>{{$user->linkedin_link ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('pinterest_link')}}</th>
                                            <td>{{$user->pinterest_link ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('youtube_link')}}</th>
                                            <td>{{$user->youtube_link ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{tr('twitch_link')}}</th>
                                            <td>{{$user->twitch_link ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('snapchat_link')}}</th>
                                            <td>{{$user->snapchat_link ?: tr('n_a')}}</td>
                                        </tr>
                                    </table>
                                </div>

                                @if($user->featured_story)
                                    <div>

                                        <hr>

                                        <h4>{{tr('featured_stories')}}</h4>
                                        <hr>
                                        <video width="400" controls>
                                            <source src="{{$user->featured_story}}" type="video/mp4" class="img-fluid" alt="{{tr('featured_stories')}}">
                                        </video>
                                    </div>
                                @endif

                            </div>

                            @include('admin.users._premium_account_form')
                    
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


@endsection

@section('scripts')

@endsection