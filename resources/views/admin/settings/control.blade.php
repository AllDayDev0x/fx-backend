@extends('layouts.admin') 

@section('title', tr('admin_control')) 

@section('content-header', tr('admin_control')) 

@section('breadcrumb')

<li class="breadcrumb-item active">{{ tr('admin_control') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-content">

                    <section id="basic-form-layouts">
    
                        <div class="row match-height">
                        
                            <div class="col-lg-12">

                                <div class="card">
                                    
                                    <div class="card-header border-bottom border-gray">
                                        <h4 class="card-title">{{tr('admin')}}</h4>
                                    </div>

                                    <div class="card-content collapse show">

                                        <div class="card-body">

                                            <form class="forms-sample" action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                                            @csrf

                                                <div class="card-header">

                                                    <h4 class="text-uppercase">{{tr('admin_control')}}</h4>

                                                    <hr>

                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_demo_control_enabled') }}</label>
                                                            <br>
                                                            <input required type="radio" id="is_demo_control_enabled_yes" class="with-gap" name="is_demo_control_enabled" value="1" @if(Setting::get('is_demo_control_enabled') == 1) checked @endif>
                                                            
                                                            <label for="is_demo_control_enabled_yes">
                                                                
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_demo_control_enabled_no" class="with-gap" name="is_demo_control_enabled"  value="0" @if(Setting::get('is_demo_control_enabled') == 0) checked @endif>

                                                            <label for="is_demo_control_enabled_no">
                                                                
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_account_email_verification') }}</label>
                                                            <br>
                                                            <input required type="radio" id="is_account_email_verification_yes" class="with-gap" name="is_account_email_verification" value="1" @if(Setting::get('is_account_email_verification') == 1) checked @endif>
                                                            <label for="is_account_email_verification_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_account_email_verification_no" class="with-gap" name="is_account_email_verification"  value="0" @if(Setting::get('is_account_email_verification') == 0) checked @endif>
                                                            <label for="is_account_email_verification_no">
                                                                
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_mailgun_email_validate') }}</label>
                                                            <br>
                                                            <input required type="radio" id="is_mailgun_email_validate_yes"  name="is_mailgun_email_validate" value="1" class="with-gap" @if(Setting::get('is_mailgun_email_validate') == 1) checked @endif>
                                                            <label for="is_mailgun_email_validate_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_mailgun_email_validate_no" name="is_mailgun_email_validate" class="with-gap"  value="0" @if(Setting::get('is_mailgun_email_validate') == 0) checked @endif>
                                                            <label for="is_mailgun_email_validate_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_email_notification') }}</label>
                                                            <br>
                                                            <input required type="radio" id="is_email_notification_yes" class="with-gap" name="is_email_notification" value="1" @if(Setting::get('is_email_notification') == 1) checked @endif>
                                                            <label for="is_email_notification_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_email_notification_no" name="is_email_notification" class="with-gap"  value="0" @if(Setting::get('is_email_notification') == 0) checked @endif>
                                                            <label for="is_email_notification_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_email_configured') }}</label>
                                                            <br>

                                                            <input required type="radio" id="is_email_configured_yes" name="is_email_configured" value="1" class="with-gap" @if(Setting::get('is_email_configured') == 1) checked @endif>
                                                            <label for="is_email_configured_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_email_configured_no" name="is_email_configured" class="with-gap"  value="0" @if(Setting::get('is_email_configured') == 0) checked @endif>
                                                            <label for="is_email_configured_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_push_notification') }}</label>
                                                            <br>

                                                            <input required type="radio" id="is_push_notification_yes" name="is_push_notification" value="1" class="with-gap" @if(Setting::get('is_push_notification') == 1) checked @endif>
                                                            <label for="is_push_notification_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_push_notification_no" name="is_push_notification" class="with-gap"  value="0" @if(Setting::get('is_push_notification') == 0) checked @endif>
                                                            <label for="is_push_notification_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('notification_count_update_enabled') }}</label>
                                                            <br>

                                                            <input required type="radio" id="notification_count_update_enabled_yes" name="is_notification_count_enabled" value="1" class="with-gap" @if(Setting::get('is_notification_count_enabled') == 1) checked @endif>
                                                            <label for="notification_count_update_enabled_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="notification_count_update_enabled_no" name="is_notification_count_enabled" class="with-gap"  value="0" @if(Setting::get('is_notification_count_enabled') == 0) checked @endif>
                                                            <label for="notification_count_update_enabled_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('s3_bucket') }}</label>
                                                            <br>

                                                            <input required type="radio" id="s3_bucket_yes" name="s3_bucket" value="1" class="with-gap" @if(Setting::get('s3_bucket') == 1) checked @endif>
                                                            <label for="s3_bucket_yes">
                                                                {{tr('enable')}}
                                                            </label>

                                                            <input required type="radio" id="s3_bucket_no" name="s3_bucket" class="with-gap"  value="0" @if(Setting::get('s3_bucket') == 0) checked @endif>
                                                            <label for="s3_bucket_no">
                                                                {{tr('disable')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_multilanguage_support') }}</label>
                                                            <br>
                                                                
                                                            <input required type="radio" id="is_multilanguage_support_yes" name="is_multilanguage_enabled" value="1" class="with-gap" @if(Setting::get('is_multilanguage_enabled') == 1) checked @endif>
                                                            <label for="is_multilanguage_support_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_multilanguage_support_no" name="is_multilanguage_enabled" class="with-gap"  value="0" @if(Setting::get('is_multilanguage_enabled') == 0) checked @endif>
                                                            <label for="is_multilanguage_support_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>


                                                        <div class="form-group col-md-6">
                                                                       
                                                           <label>{{ tr('is_watermark_logo_enabled') }}</label>
                                                           <br>

                                                            <input required type="radio" id="is_watermark_logo_enabled_yes" name="is_watermark_logo_enabled" value="1" class="with-gap" @if(Setting::get('is_watermark_logo_enabled') == 1) checked @endif>
                                                           <label for="is_watermark_logo_enabled_yes">
                                                               {{tr('yes')}}
                                                           </label>

                                                            <input required type="radio" id="is_watermark_logo_enabled_no" name="is_watermark_logo_enabled" class="with-gap"  value="0" @if(Setting::get('is_watermark_logo_enabled') == 0) checked @endif>
                                                            <label for="is_watermark_logo_enabled_no">
                                                               {{tr('no')}}
                                                            </label>
                                                   
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                           <label>{{ tr('is_ccbill_enabled') }}</label>
                                                           <br>

                                                            <input required type="radio" id="is_ccbill_enabled_yes" name="is_ccbill_enabled" value="1" class="with-gap" @if(Setting::get('is_ccbill_enabled') == 1) checked @endif>
                                                           <label for="is_ccbill_enabled_yes">
                                                               {{tr('yes')}}
                                                           </label>

                                                            <input required type="radio" id="is_ccbill_enabled_no" name="is_ccbill_enabled" class="with-gap"  value="0" @if(Setting::get('is_ccbill_enabled') == 0) checked @endif>
                                                           <label for="is_ccbill_enabled_no">
                                                               {{tr('no')}}
                                                           </label>
                                                   
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                           <label>{{ tr('is_wallet_payment_enabled') }}</label>
                                                           <br>

                                                            <input required type="radio" id="is_wallet_payment_enabled_yes" name="is_wallet_payment_enabled" value="1" class="with-gap" @if(Setting::get('is_wallet_payment_enabled') == 1) checked @endif>

                                                           <label for="is_wallet_payment_enabled_yes">                                                               {{tr('yes')}}
                                                           </label>

                                                            <input required type="radio" id="is_wallet_payment_enabled_no" name="is_wallet_payment_enabled" class="with-gap"  value="0" @if(Setting::get('is_wallet_payment_enabled') == 0) checked @endif>
                                                           <label for="is_wallet_payment_enabled_no">
                                                               {{tr('no')}}
                                                           </label>
                                                   
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                           <label>{{ tr('is_stripe_enabled') }}</label>
                                                           <br>

                                                            <input required type="radio" id="is_stripe_enabled_yes" name="is_stripe_enabled" value="1" class="with-gap" @if(Setting::get('is_stripe_enabled') == 1) checked @endif>

                                                           <label for="is_stripe_enabled_yes">   
                                                                {{tr('yes')}}
                                                           </label>

                                                            <input required type="radio" id="is_stripe_enabled_no" name="is_stripe_enabled" class="with-gap"  value="0" @if(Setting::get('is_stripe_enabled') == 0) checked @endif>
                                                           <label for="is_stripe_enabled_no">
                                                               {{tr('no')}}
                                                           </label>
                                                   
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                           <label>{{ tr('is_coinpayment_enabled') }}</label>
                                                           <br>

                                                            <input required type="radio" id="is_coinpayment_enabled_yes" name="is_coinpayment_enabled" value="1" class="with-gap" @if(Setting::get('is_coinpayment_enabled') == 1) checked @endif>
                                                           <label for="is_coinpayment_enabled_yes">
                                                               {{tr('yes')}}
                                                           </label>

                                                            <input required type="radio" id="is_coinpayment_enabled_no" name="is_coinpayment_enabled" class="with-gap"  value="0" @if(Setting::get('is_coinpayment_enabled') == 0) checked @endif>
                                                           <label for="is_coinpayment_enabled_no">
                                                               {{tr('no')}}
                                                           </label>
                                                   
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                           <label>{{ tr('is_only_wallet_payment') }}</label>
                                                           <br>

                                                            <input required type="radio" id="is_only_wallet_payment_yes" name="is_only_wallet_payment" value="1" class="with-gap" @if(Setting::get('is_only_wallet_payment') == 1) checked @endif>
                                                           <label for="is_only_wallet_payment_yes">
                                                               {{tr('yes')}}
                                                           </label>

                                                            <input required type="radio" id="is_only_wallet_payment_no" name="is_only_wallet_payment" class="with-gap"  value="0" @if(Setting::get('is_only_wallet_payment') == 0) checked @endif>
                                                           <label for="is_only_wallet_payment_no">
                                                               {{tr('no')}}
                                                           </label>
                                                   
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                           <label>{{ tr('is_web_notification_enabled') }}</label>
                                                           <br>

                                                            <input required type="radio" id="is_web_notification_yes" name="is_web_notification_enabled" value="1" class="with-gap" @if(Setting::get('is_web_notification_enabled') == 1) checked @endif>
                                                           <label for="is_web_notification_yes">
                                                               {{tr('yes')}}
                                                           </label>

                                                            <input required type="radio" id="is_web_notification_no" name="is_web_notification_enabled" class="with-gap"  value="0" @if(Setting::get('is_web_notification_enabled') == 0) checked @endif>
                                                           <label for="is_web_notification_no">
                                                               {{tr('no')}}
                                                           </label>
                                                   
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                           <label>{{ tr('buy_single_user_products') }}</label>
                                                           <br>

                                                            <input required type="radio" id="buy_single_user_products_yes" name="buy_single_user_products" value="1" class="with-gap" @if(Setting::get('buy_single_user_products') == 1) checked @endif>
                                                           <label for="buy_single_user_products_yes">
                                                               {{tr('yes')}}
                                                           </label>

                                                            <input required type="radio" id="buy_single_user_products_no" name="buy_single_user_products" class="with-gap"  value="0" @if(Setting::get('buy_single_user_products') == 0) checked @endif>
                                                           <label for="buy_single_user_products_no">
                                                               {{tr('no')}}
                                                           </label>
                                                   
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                           <label>{{ tr('user_online_status_limit') }}</label>

                                                            <input type="text" id="user_online_status_limit" name="user_online_status_limit" value="{{Setting::get('user_online_status_limit')}}" class="form-control">
                                                   
                                                        </div>

                                                        <div class="row">

                                                        <div class="col-md-12">

                                                            <hr><h4>Verified Badge Settings</h4><hr>

                                                        </div>

                                                            <div class="form-group col-md-6">
                                                                           
                                                                <label>{{ tr('is_verified_badge_enabled') }}</label>
                                                                <br>

                                                                <input required type="radio" id="is_verified_badge_enabled_yes" name="is_verified_badge_enabled" value="1" class="with-gap" @if(Setting::get('is_verified_badge_enabled') == 1) checked @endif>
                                                                <label for="is_verified_badge_enabled_yes">
                                                                    {{tr('enable')}}
                                                                </label>

                                                                <input required type="radio" id="is_verified_badge_enabled_no" name="is_verified_badge_enabled" class="with-gap"  value="0" @if(Setting::get('is_verified_badge_enabled') == 0) checked @endif>
                                                                <label for="is_verified_badge_enabled_no">
                                                                    {{tr('disable')}}
                                                                </label>
                                                        
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                           
                                                                <label>{{ tr('verified_badge_text') }}</label>
                                                                
                                                                <input type="text" name="verified_badge_text" class="form-control" value="{{Setting::get('verified_badge_text')}}">
                                                        
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label for="verified_badge_file">{{tr('verified_badge_file')}} *</label>
                                                                <p class="txt-warning">{{tr('png_image_note')}}</p>
                                                                <input type="file" class="form-control" id="verified_badge_file" name="verified_badge_file" accept="image/png" placeholder="{{tr('verified_badge_file')}}">

                                                                @if(Setting::get('verified_badge_file'))

                                                                    <img class="img img-thumbnail m-b-20" style="width: 20%" src="{{Setting::get('verified_badge_file')}}" alt="{{Setting::get('site_name')}}"> 

                                                                @endif
                                                            
                                                            </div>

                                                        </div>
                                                        
                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('paypal_payment_status') }}</label>
                                                            <br>

                                                            <input required type="radio" id="paypal_payment_status_yes" name="is_paypal_enabled" value="1" class="with-gap" @if(Setting::get('is_paypal_enabled') == 1) checked @endif>
                                                            <label for="paypal_payment_status_yes">
                                                                {{tr('enable')}}
                                                            </label>

                                                            <input required type="radio" id="paypal_payment_status_no" name="is_paypal_enabled" class="with-gap"  value="0" @if(Setting::get('is_paypal_enabled') == 0) checked @endif>
                                                            <label for="paypal_payment_status_no">
                                                                {{tr('disable')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_user_active_status') }}</label>
                                                            <br>

                                                            <input required type="radio" id="is_user_active_status_yes" name="is_user_active_status" value="1" class="with-gap" @if(Setting::get('is_user_active_status') == 1) checked @endif>
                                                            <label for="is_user_active_status_yes">
                                                                {{tr('enable')}}
                                                            </label>

                                                            <input required type="radio" id="is_user_active_status_no" name="is_user_active_status" class="with-gap"  value="0" @if(Setting::get('is_user_active_status') == 0) checked @endif>
                                                            <label for="is_user_active_status_no">
                                                                {{tr('disable')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="clearfix"></div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('admin_take_count') }}</label>
                                                            
                                                            <input type="number" name="admin_take_count" class="form-control" value="{{Setting::get('admin_take_count', 6)}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('currency') }}</label>
                                                            
                                                            <input type="text" name="currency" class="form-control" value="{{Setting::get('currency', '$')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('currency_code') }}</label>
                                                            
                                                            <input type="text" name="currency_code" class="form-control" value="{{Setting::get('currency_code', 'USD')}}">
                                                    
                                                        </div>

                                                    </div>

                                                    <div class="clearfix"></div>

                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <hr><h4>Demo Login Details</h4><hr>

                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('demo_admin_email') }}</label>
                                                            
                                                            <input type="text" name="demo_admin_email" class="form-control" value="{{Setting::get('demo_admin_email')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('demo_admin_password') }}</label>
                                                            
                                                            <input type="text" name="demo_admin_password" class="form-control" value="{{Setting::get('demo_admin_password')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('demo_user_email') }}</label>
                                                            
                                                            <input type="text" name="demo_user_email" class="form-control" value="{{Setting::get('demo_user_email')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('demo_user_password') }}</label>
                                                            
                                                            <input type="text" name="demo_user_password" class="form-control" value="{{Setting::get('demo_user_password')}}">
                                                    
                                                        </div>
                                                    
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <hr><h4>Frontend Settings</h4><hr>

                                                        </div>

                                                        <div class="col-md-6">

                                                            <div class="form-group">
                                                                <label for="frontend_no_data_image">{{tr('frontend_no_data_image')}} *</label>
                                                                <input type="file" class="form-control" id="frontend_no_data_image" name="frontend_no_data_image" accept="image/png" placeholder="{{tr('frontend_no_data_image')}}">
                                                            </div>
                                                            
                                                            @if(Setting::get('frontend_no_data_image'))

                                                                <img class="img img-thumbnail m-b-20" style="width: 40%" src="{{Setting::get('frontend_no_data_image')}}" alt="{{Setting::get('site_name')}}"> 

                                                            @endif

                                                        </div>
                                                    
                                                    </div>

                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <hr><h4>Push Notification Links</h4><hr>

                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_USER_FOLLOWINGS') }}</label>
                                                            
                                                            <input type="text" name="BN_USER_FOLLOWINGS" class="form-control" value="{{Setting::get('BN_USER_FOLLOWINGS')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_USER_COMMENT') }}</label>
                                                            
                                                            <input type="text" name="BN_USER_COMMENT" class="form-control" value="{{Setting::get('BN_USER_COMMENT')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_USER_LIKE') }}</label>
                                                            
                                                            <input type="text" name="BN_USER_LIKE" class="form-control" value="{{Setting::get('BN_USER_LIKE')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_USER_TIPS') }}</label>
                                                            
                                                            <input type="text" name="BN_USER_TIPS" class="form-control" value="{{Setting::get('BN_USER_TIPS')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_USER_VIDEO_CALL') }}</label>
                                                            
                                                            <input type="text" name="BN_USER_VIDEO_CALL" class="form-control" value="{{Setting::get('BN_USER_VIDEO_CALL')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_USER_AUDIO_CALL') }}</label>
                                                            
                                                            <input type="text" name="BN_USER_AUDIO_CALL" class="form-control" value="{{Setting::get('BN_USER_AUDIO_CALL')}}">
                                                    
                                                        </div>



                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_CHAT_MESSAGE') }}</label>
                                                            
                                                            <input type="text" name="BN_CHAT_MESSAGE" class="form-control" value="{{Setting::get('BN_CHAT_MESSAGE')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_LIVE_VIDEO') }}</label>
                                                            
                                                            <input type="text" name="BN_LIVE_VIDEO" class="form-control" value="{{Setting::get('BN_LIVE_VIDEO')}}">
                                                    
                                                        </div>
                                                    
                                                    </div>

                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <hr><h4>Live Video Call Settings</h4><hr>

                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_agora_configured') }}</label>
                                                            <br>

                                                            <input required type="radio" id="is_agora_configured_yes" name="is_agora_configured" value="1" class="with-gap" @if(Setting::get('is_agora_configured') == 1) checked @endif>
                                                            <label for="is_agora_configured_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_agora_configured_no" name="is_agora_configured" class="with-gap"  value="0" @if(Setting::get('is_agora_configured') == 0) checked @endif>
                                                            <label for="is_agora_configured_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_one_to_one_call_enabled') }}</label>
                                                            <br>

                                                            <input required type="radio" id="is_one_to_one_call_enabled_yes" name="is_one_to_one_call_enabled" value="1" class="with-gap" @if(Setting::get('is_one_to_one_call_enabled') == 1) checked @endif>
                                                            <label for="is_one_to_one_call_enabled_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_one_to_one_call_enabled_no" name="is_one_to_one_call_enabled" class="with-gap"  value="0" @if(Setting::get('is_one_to_one_call_enabled') == 0) checked @endif>
                                                            <label for="is_one_to_one_call_enabled_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>
                                                        
                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_one_to_many_call_enabled') }}</label>
                                                            <br>
                                                            <input required type="radio" id="is_one_to_many_call_enabled_yes" name="is_one_to_many_call_enabled" value="1" class="with-gap" @if(Setting::get('is_one_to_many_call_enabled') == 1) checked @endif>
                                                            <label for="is_one_to_many_call_enabled_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_one_to_many_call_enabled_no" name="is_one_to_many_call_enabled" class="with-gap"  value="0" @if(Setting::get('is_one_to_many_call_enabled') == 0) checked @endif>
                                                            <label for="is_one_to_many_call_enabled_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>


                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('agora_app_id') }}</label>
                                                            
                                                            <input type="text" name="agora_app_id" class="form-control" value="{{Setting::get('agora_app_id')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('agora_certificate_id') }}</label>
                                                            
                                                            <input type="text" name="agora_certificate_id" class="form-control" value="{{Setting::get('agora_certificate_id')}}">
                                                    
                                                        </div>
                                                    
                                                    </div>

                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <hr><h4>Live Video Call Settings</h4><hr>

                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('NOCAPTCHA_SECRET_KEY') }}</label>
                                                            
                                                            <input type="text" name="NOCAPTCHA_SECRET_KEY" class="form-control" value="{{Setting::get('NOCAPTCHA_SECRET_KEY')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('NOCAPTCHA_SITE_KEY') }}</label>
                                                            
                                                            <input type="text" name="NOCAPTCHA_SITE_KEY" class="form-control" value="{{Setting::get('NOCAPTCHA_SITE_KEY')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_captcha_enabled') }}</label>
                                                            <br>
                                                            <input required type="radio" id="is_captcha_enabled_yes" name="is_captcha_enabled" value="1" class="with-gap" @if(Setting::get('is_captcha_enabled') == 1) checked @endif>
                                                            <label for="is_captcha_enabled_yes">
                                                                {{tr('yes')}}
                                                            </label>

                                                            <input required type="radio" id="is_captcha_enabled_no" name="is_captcha_enabled" class="with-gap"  value="0" @if(Setting::get('is_captcha_enabled') == 0) checked @endif>
                                                            <label for="is_captcha_enabled_no">
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>
                                                    
                                                    </div>
                                                
                                                </div>

                                                <div class="form-actions">

                                                    <div class="pull-right">
                                                    
                                                        <button type="reset" class="btn btn-warning mr-1">
                                                            <i class="ft-x"></i> {{ tr('reset') }} 
                                                        </button>

                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                                                    
                                                    </div>

                                                    <div class="clearfix"></div>

                                                </div>

                                            </form>
                                            
                                        </div>
                                    
                                    </div>

                                </div>
                            
                            </div>
                        
                        </div>

                    </section>

                </div>

            </div>

            <div class="card">

                <div class="card-content">

                    <section id="basic-form-layouts">
    
                        <div class="row match-height">
                        
                            <div class="col-lg-12">

                                <div class="card">
                                    
                                    <div class="card-header border-bottom border-gray">
                                        <h4 class="card-title">{{tr('admin')}}</h4>
                                    </div>

                                    <div class="card-content collapse show">

                                        <div class="card-body">


                                            <form class="forms-sample" action="{{route('admin.env-settings.save')}}" method="POST" role="form">

                                            @csrf

                                                <div class="card-header bg-card-header ">

                                                    <h4 class="">{{tr('s3_bucket_config')}}</h4>

                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('AWS_ACCESS_KEY_ID') }}</label>
                                                            <br>
                                                            <input type="text" class="form-control" id="AWS_ACCESS_KEY_ID" name="AWS_ACCESS_KEY_ID" placeholder="Enter {{tr('AWS_ACCESS_KEY_ID')}}" value="{{old('AWS_ACCESS_KEY_ID') ?: $env_values['AWS_ACCESS_KEY_ID'] }}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('AWS_SECRET_ACCESS_KEY') }}</label>
                                                            <br>
                                                            <input type="text" class="form-control" id="AWS_SECRET_ACCESS_KEY" name="AWS_SECRET_ACCESS_KEY" placeholder="Enter {{tr('AWS_SECRET_ACCESS_KEY')}}" value="{{old('AWS_SECRET_ACCESS_KEY') ?: $env_values['AWS_SECRET_ACCESS_KEY'] }}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('AWS_DEFAULT_REGION') }}</label>
                                                            <br>
                                                            <input type="text" class="form-control" id="AWS_DEFAULT_REGION" name="AWS_DEFAULT_REGION" placeholder="Enter {{tr('AWS_DEFAULT_REGION')}}" value="{{old('AWS_DEFAULT_REGION') ?: $env_values['AWS_DEFAULT_REGION'] }}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('AWS_BUCKET') }}</label>
                                                            <br>
                                                            <input type="text" class="form-control" id="AWS_BUCKET" name="AWS_BUCKET" placeholder="Enter {{tr('AWS_BUCKET')}}" value="{{old('AWS_BUCKET') ?: $env_values['AWS_BUCKET'] }}">
                                                    
                                                        </div>

                                                    </div>
                                                
                                                </div>

                                                <div class="form-actions">

                                                    <div class="pull-right">
                                                    
                                                        <button type="reset" class="btn btn-warning mr-1">
                                                            <i class="ft-x"></i> {{ tr('reset') }} 
                                                        </button>

                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                                                    
                                                    </div>

                                                    <div class="clearfix"></div>

                                                </div>

                                            </form>
                                            
                                        </div>
                                    
                                    </div>

                                </div>
                            
                            </div>
                        
                        </div>

                    </section>

                </div>

            </div>

        </div>

    </div>
    
</section>

@endsection 