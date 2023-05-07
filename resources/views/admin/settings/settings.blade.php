@extends('layouts.admin') 

@section('title', tr('settings'))

@section('content-header', tr('settings'))

@section('breadcrumb')

<li class="breadcrumb-item active" aria-current="page">{{ tr('settings') }}</li>

@endsection 

@section('styles')

<style>
    
/*  fx tab */
div.fx-tab-container{
    z-index: 10;
    background-color: #ffffff;
    padding: 0 !important;
    border-radius: 4px;
    -moz-border-radius: 4px;
    border:1px solid #ddd;
    margin-top: 20px;
    margin-left: 50px;
    -webkit-box-shadow: 0 6px 12px rgba(3, 169, 243, 0.5);
    box-shadow: 0 6px 12px rgba(3, 169, 243, 0.5);
    -moz-box-shadow: 0 6px 12px rgba(3, 169, 243, 0.5);
    background-clip: padding-box;
    opacity: 0.97;
    filter: alpha(opacity=97);
}
div.fx-tab-menu{
    padding-right: 0;
    padding-left: 0;
    padding-bottom: 0;
}
div.fx-tab-menu div.list-group{
    margin-bottom: 0;
}
div.fx-tab-menu div.list-group>a{
    margin-bottom: 0;
}
div.fx-tab-menu div.list-group>a .glyphicon,
div.fx-tab-menu div.list-group>a .fa {
    color: #fea600;
}
div.fx-tab-menu div.list-group>a:first-child{
    border-top-right-radius: 0;
    -moz-border-top-right-radius: 0;
}
div.fx-tab-menu div.list-group>a:last-child{
    border-bottom-right-radius: 0;
    -moz-border-bottom-right-radius: 0;
}
div.fx-tab-menu div.list-group>a.active,
div.fx-tab-menu div.list-group>a.active .glyphicon,
div.fx-tab-menu div.list-group>a.active .fa{
    background-color: var(--btn-primary-color);
    background-image: #fea600;
    color: #b1af60;
    border: 2px dashed;
}
div.fx-tab-menu div.list-group>a.active:after{
    content: '';
    position: absolute;
    left: 100%;
    top: 50%;
    margin-top: -13px;
    border-left: 0;
    border-bottom: 13px solid transparent;
    border-top: 13px solid transparent;
    border-left: 10px solid var(--btn-primary-color);
}

div.fx-tab-content{
    background-color: #ffffff;
    /* border: 1px solid #eeeeee; */
    padding-left: 20px;
    padding-top: 10px;
}

.box-body {
    padding: 0px;
}

div.fx-tab div.fx-tab-content:not(.active){
  display: none;
}

.sub-title {
    width: fit-content;
    color: #2c648c;
    font-size: 18px;
    /*border-bottom: 2px dashed #285a86;*/
    padding-bottom: 5px;
}

hr {
    margin-top: 15px;
    margin-bottom: 15px;
}

.settings-sub-header {
    color: #f30660 !important;
}
</style>
@endsection

@section('content')

<section class="content">

<div class="box-body">
    <div class="callout bg-pale-secondary" style="background: #fff">
            <h4>{{tr('notes')}}</h4>
             <p>
                </p><ul>
                    <li>
                        {{tr('settings_note')}}
                    </li>
                </ul>
            <p>
        </p>
    </div>
</div>
<br>

<div class="row settings-sec">
    
     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 fx-tab-menu resp-mrg-btm-xs">
        
        <div class="list-group">
            <a href="#" class="list-group-item active text-left text-uppercase">
                {{tr('site_settings')}}
            </a>
        
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('payment_settings')}}
            </a>

            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('email_settings')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('social_settings')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('social_login')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('notification_settings')}}
            </a>

            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('live_video_settings')}}
            </a>

            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('image_settings')}}
            </a>

            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('mobile_settings')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('contact_information')}}
            </a>

            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('configuration_settings')}}
            </a>

            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('other_settings')}}
            </a>

            @if(Setting::get('is_watermark_logo_enabled'))

            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('watermark_settings')}}
            </a>

            @endif            

        </div>

    </div>
    
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 fx-tab">
        
        <!-- Site section -->            
        <div class="fx-tab-content active">

           <form id="site_settings_save" action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                @csrf

                <div class="box-body">

                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase"><b>{{tr('site_settings')}}</b></h5>
                            <hr>

                        </div>

                        <div class="col-md-12 col-xl-6 col-lg-6">

                            <div class="form-group">
                                <label for="site_name">{{tr('site_name')}} *</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" placeholder="Enter {{tr('site_name')}}" value="{{Setting::get('site_name')}}">
                            </div>

                            <div class="form-group">
                                <label for="tag_name">{{tr('tag_name')}} *</label>
                                <input type="text" class="form-control" id="tag_name" name="tag_name" placeholder="Enter {{tr('tag_name')}}" value="{{Setting::get('tag_name')}}">
                            </div>

                            <div class="form-group">
                                <label for="site_logo">{{tr('site_logo')}} *</label>
                                <p class="txt-warning">{{tr('png_image_note')}}</p>
                                <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/png" placeholder="{{tr('site_logo')}}">
                            </div>
                            
                            @if(Setting::get('site_logo'))

                                <img class="img img-thumbnail m-b-20" style="width: 40%" src="{{Setting::get('site_logo')}}" alt="{{Setting::get('site_name')}}"> 

                            @endif

                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-12">

                            <div class="form-group">

                                <label for="frontend_url">{{tr('frontend_url')}} *</label>

                                <input type="text" class="form-control" id="frontend_url" name="frontend_url" placeholder="{{tr('frontend_url')}}" value="{{Setting::get('frontend_url')}}">

                            </div>

                            <div class="form-group">

                                <label for="site_icon">{{tr('site_icon')}} *</label>

                                <p class="txt-warning">{{tr('png_image_note')}}</p>

                                <input type="file" class="form-control" id="site_icon" name="site_icon" accept="image/png" placeholder="{{tr('site_icon')}}">

                            </div>

                            @if(Setting::get('site_icon'))

                                <img class="img img-thumbnail m-b-20" style="width: 20%" src="{{Setting::get('site_icon')}}" alt="{{Setting::get('site_name')}}"> 

                            @endif

                        </div>

                    </div>

                </div>

                <!-- /.box-body -->

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

            <br>

        </div>

        <!-- Payment settings -->
        <div class="fx-tab-content">
            
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST" enctype="multipart/form-data" class="forms-sample">
         
            @csrf

                <div class="box-body">

                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('payment_settings')}}</b></h5>

                            <hr>

                        </div>

                        @if(Setting::get('is_stripe_enabled') == YES)

                        <div class="col-md-12">

                            <h5 class="sub-title">{{tr('stripe_settings')}}</h5>

                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">

                                <label for="stripe_publishable_key">{{tr('stripe_publishable_key')}} *</label>

                                <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" placeholder="Enter {{tr('stripe_publishable_key')}}" value="{{old('stripe_publishable_key') ?: Setting::get('stripe_publishable_key')}}">

                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="stripe_secret_key">{{tr('stripe_secret_key')}} *</label>

                                <input type="text" class="form-control" id="stripe_secret_key" name="stripe_secret_key" placeholder="Enter {{tr('stripe_secret_key')}}" value="{{old('stripe_secret_key') ?: Setting::get('stripe_secret_key')}}">
                            </div>
                        
                        </div>

                        @endif

                        @if(Setting::get('is_paypal_enabled') == YES)

                        <div class="col-md-12">

                            <hr>

                            <h5 class="sub-title">{{tr('paypal_settings')}}</h5>

                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="PAYPAL_MODE">{{tr('PAYPAL_MODE')}}</label> 

                                <div class="clearfix"></div>
                                
                                <input type="radio" name="PAYPAL_MODE" value="{{PRODUCTION}}" id="paypal_live" @if($env_values['PAYPAL_MODE'] == PRODUCTION ) checked @endif onchange="checkPaypalType(this.value)">
                                 <label for="paypal_live">
                                   {{ tr('paypal_live') }}
                                </label>

                                <input type="radio" name="PAYPAL_MODE" value="{{SANDBOX}}" id="paypal_sandbox" @if($env_values['PAYPAL_MODE'] == SANDBOX ) checked @endif onchange="checkPaypalType(this.value)">
                                 <label for="paypal_sandbox">
                                   {{ tr('paypal_sandbox') }}
                                </label>
                                
                            </div>
                        </div>

                       
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="paypal_id">{{ tr('PAYPAL_SANDBOX_API_USERNAME') }}</label>
                                <input type="text" class="form-control" name="PAYPAL_SANDBOX_API_USERNAME" id="PAYPAL_SANDBOX_API_PASSWORD" placeholder="{{ tr('PAYPAL_SANDBOX_API_USERNAME') }}" value="{{ $env_values['PAYPAL_SANDBOX_API_USERNAME'] ?? ''}}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="paypal_secret">{{ tr('PAYPAL_SANDBOX_API_PASSWORD') }}</label>    
                                <input type="text" class="form-control" name="PAYPAL_SANDBOX_API_PASSWORD" id="PAYPAL_SANDBOX_API_PASSWORD" placeholder="{{ tr('PAYPAL_SANDBOX_API_PASSWORD') }}" value="{{ $env_values['PAYPAL_SANDBOX_API_PASSWORD'] ?? ''}}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="paypal_secret">{{ tr('PAYPAL_SANDBOX_API_SECRET') }}</label>    
                                <input type="text" class="form-control" name="PAYPAL_SANDBOX_API_SECRET" id="PAYPAL_SANDBOX_API_SECRET" placeholder="{{ tr('PAYPAL_SANDBOX_API_SECRET') }}" value="{{ $env_values['PAYPAL_SANDBOX_API_SECRET'] ?? ''}}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="paypal_id">{{ tr('PAYPAL_LIVE_API_USERNAME') }}</label>
                                <input type="text" class="form-control" name="PAYPAL_LIVE_API_USERNAME" id="PAYPAL_LIVE_API_USERNAME" placeholder="{{ tr('PAYPAL_LIVE_API_USERNAME') }}" value="{{ $env_values['PAYPAL_LIVE_API_USERNAME'] ?? ''}}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="paypal_secret">{{ tr('PAYPAL_LIVE_API_PASSWORD') }}</label>    
                                <input type="text" class="form-control" name="PAYPAL_LIVE_API_PASSWORD" id="PAYPAL_LIVE_API_PASSWORD" placeholder="{{ tr('PAYPAL_LIVE_API_PASSWORD') }}" value="{{ $env_values['PAYPAL_LIVE_API_PASSWORD'] ?? '' }}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="paypal_secret">{{ tr('PAYPAL_LIVE_API_SECRET') }}</label>    
                                <input type="text" class="form-control" name="PAYPAL_LIVE_API_SECRET" id="PAYPAL_LIVE_API_SECRET" placeholder="{{ tr('PAYPAL_LIVE_API_SECRET') }}" value="{{ $env_values['PAYPAL_LIVE_API_SECRET'] ?? ''}}">
                            </div>
                        </div>

                        @endif

                        @if(Setting::get('is_ccbill_enabled') == YES)

                        <div class="col-md-12">

                            <hr>

                            <h5 class="sub-title">{{tr('ccbill_settings')}}</h5>

                        </div>

                       
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="ccbill_url">{{ tr('ccbill_url') }}</label>
                                <input type="text" class="form-control" name="ccbill_url" id="ccbill_url" placeholder="{{ tr('ccbill_url') }}" value="{{Setting::get('ccbill_url') ?? tr("n_a") }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="ccbill_account_number">{{ tr('ccbill_account_number') }}</label>
                                <input type="text" class="form-control" name="ccbill_account_number" id="ccbill_account_number" placeholder="{{ tr('ccbill_account_number') }}" value="{{Setting::get('ccbill_account_number') ?? tr("n_a")}}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="ccbill_sub_account_number">{{ tr('ccbill_sub_account_number') }}</label>    
                                <input type="text" class="form-control" name="ccbill_sub_account_number" id="ccbill_sub_account_number" placeholder="{{ tr('ccbill_sub_account_number') }}" value="{{Setting::get('ccbill_sub_account_number') ?? tr("n_a")}}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="flex_form_id">{{ tr('flex_form_id') }}</label>
                                <input type="text" class="form-control" name="flex_form_id" id="flex_form_id" placeholder="{{ tr('flex_form_id') }}" value="{{Setting::get('flex_form_id') ?? tr("n_a")}}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="salt_key">{{ tr('salt_key') }}</label>    
                                <input type="text" class="form-control" name="salt_key" id="salt_key" placeholder="{{ tr('salt_key') }}" value="{{Setting::get('salt_key') ?? tr("n_a")}}">
                            </div>
                        </div>

                        @endif

                    </div>

                    <div class="row">

                        <div class="col-md-12">

                            <hr>

                            <h5 class="sub-title">{{tr('revenue_settings')}}</h5>

                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="admin_commission">{{tr('post_admin_commission')}} (%)</label>
                                <p class="text-muted">{{tr('post_payments_settings_notes')}}</p>
                                <input type="text" class="form-control" name="admin_commission" pattern="[0-9]{0,}" value="{{Setting::get('admin_commission')  }}" id="admin_commission" placeholder="{{tr('admin_commission')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="tips_admin_commission">{{tr('tips_admin_commission')}} (%)</label>
                                <p class="text-muted">{{tr('tips_admin_commission_notes')}}</p>
                                <input type="text" class="form-control" name="tips_admin_commission" pattern="[0-9]{0,}" value="{{Setting::get('tips_admin_commission')  }}" id="tips_admin_commission" placeholder="{{tr('tips_admin_commission')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="live_streaming_admin_commission">{{tr('live_streaming_admin_commission')}} (%)</label>
                                <p class="text-muted">{{tr('live_streaming_admin_commission_notes')}}</p>

                                <input type="text" class="form-control" name="live_streaming_admin_commission" pattern="[0-9]{0,}" value="{{Setting::get('live_streaming_admin_commission')  }}" id="live_streaming_admin_commission" placeholder="{{tr('live_streaming_admin_commission')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="video_call_admin_commission">{{tr('video_call_admin_commission')}} (%)</label>
                                <p class="text-muted">{{tr('video_call_admin_commission_notes')}}</p>
                                <input type="text" class="form-control" name="video_call_admin_commission" pattern="[0-9]{0,}" value="{{Setting::get('video_call_admin_commission')}}" id="video_call_admin_commission" placeholder="{{tr('video_call_admin_commission')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="audio_call_admin_commission">{{tr('audio_call_admin_commission')}} (%)</label>
                                <p class="text-muted">{{tr('audio_call_admin_commission_notes')}}</p>
                                <input type="text" class="form-control" name="audio_call_admin_commission" pattern="[0-9]{0,}" value="{{Setting::get('audio_call_admin_commission')}}" id="audio_call_admin_commission" placeholder="{{tr('audio_call_admin_commission')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="subscription_admin_commission">{{tr('subscription_admin_commission')}} (%)</label>
                                <p class="text-muted">{{tr('subscription_admin_commission_notes')}}</p>
                                <input type="text" class="form-control" name="subscription_admin_commission" pattern="[0-9]{0,}" value="{{Setting::get('subscription_admin_commission')  }}" id="subscription_admin_commission" placeholder="{{tr('subscription_admin_commission')}}">
                            </div>
                        </div>

                    </div>


                    @if(Setting::get('is_referral_enabled') == YES)
                     <div class="row">

                        <div class="col-md-12">

                            <hr>

                            <h5 class="sub-title">{{tr('referral_code_earnings')}}</h5>

                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="admin_commission">{{tr('referral_earnings')}}(in {{tr('token')}})</label>
                                <p class="text-muted">{{tr('referral_earnings_notes')}}</p>

                                 <input type="number" class="form-control" name="referral_earnings" value="{{ old('referral_earnings') ?: Setting::get('referral_earnings') }}" id="referral_earnings" min="0" maxlength="100" pattern="[0-9]{0,}" placeholder="{{ tr('referral_earnings') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="tips_admin_commission">{{tr('referrer_earnings')}}(in {{tr('token')}})</label>
                                <p class="text-muted">{{tr('referrer_earnings_notes')}}</p>

                                <input type="number" class="form-control" name="referrer_earnings" value="{{ old('referrer_earnings') ?: Setting::get('referrer_earnings') }}" min="0"maxlength="100" pattern="[0-9]{0,}" id="referrer_earnings" placeholder="{{ tr('referrer_earnings') }}">
                            </div>
                        </div>

                      
                    </div>
                    @endif

                    <div class="row">

                        <div class="col-md-12">

                            <hr>

                            <h5 class="sub-title">{{tr('token_settings')}}</h5>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="tip_min_token">{{tr('tip_min_token')}}</label>
                                <input type="number" min="0" step="any" class="form-control" name="tip_min_token" pattern="[0-9]{0,}" value="{{Setting::get('tip_min_token')  }}" id="tip_min_token" placeholder="{{tr('tip_min_token')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="tip_max_token">{{tr('tip_max_token')}}</label>
                                <input type="number" min="0" step="any" class="form-control" name="tip_max_token" pattern="[0-9]{0,}" value="{{Setting::get('tip_max_token')  }}" id="tip_max_token" placeholder="{{tr('tip_max_token')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="token_amount">{{tr('token_amount')}}(in {{Setting::get('currency') ?? '$'}})</label>
                                <input type="number" min="0" step="any" class="form-control" name="token_amount" pattern="[0-9]{0,}" value="{{Setting::get('token_amount')  }}" id="token_amount" placeholder="{{tr('token_amount')}}">

                                <p class="txt-warning">{{tr('token_amount_convo')}}</p>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">

                            <hr>

                            <h5 class="sub-title">{{tr('currency_settings')}}</h5>

                        </div>

                        <!-- <div class="col-md-6">
                            <input type="hidden" name="currency" value="{{ Setting::get('currency') }}" id="currency">
                            <div class="form-group">
                                <label for="currency_code">{{tr('currency_code')}}</label>
                                <select class="form-control select2" name="currency_code" onchange="setCurrency(this.options[this.selectedIndex].id)">
                                    <option value="" disabled>{{tr('select_currency_code')}}</option>
                                    @foreach($currencies as $currency)
                                    <option value="{{ $currency->currency_code }}" id="currency_code_{{$currency->id}}" data-currency={{ $currency->currency }} @if(old('currency_code') == $currency->currency_code || $currency->currency_code == Setting::get('currency_code')) selected @endif> 
                                        {{ $currency->currency_code ? : tr('na') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> -->

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="token_symbol">{{tr('token_symbol')}}</label>
                                <input type="text" class="form-control" name="token_symbol" value="{{Setting::get('token_symbol')}}" id="token_symbol" placeholder="{{tr('token_symbol')}}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="symbol_position">{{tr('symbol_position')}}</label> 
                                <div class="clearfix"></div> 
                                <input type="radio" name="symbol_position" value="{{PREFIX}}" id="symbol_position_prefix" @if(Setting::get('symbol_position') == PREFIX ) checked @endif>
                                 <label for="symbol_position_prefix"> {{ tr('prefix') }} </label>
                                <input type="radio" name="symbol_position" value="{{SUFFIX}}" id="symbol_position_suffix" @if(Setting::get('symbol_position') == SUFFIX ) checked @endif>
                                 <label for="symbol_position_suffix"> {{ tr('suffix') }}</label>
                            </div>
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
       
            <br>
       
        </div>

        <!-- Email settings -->
        <div class="fx-tab-content">
            <form id="site_settings_save" action="{{route('admin.env-settings.save')}}" method="POST">

            @csrf
        
                <div class="box-body">

                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase"><b>{{tr('email_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                    <label for="MAIL_MAILER">{{tr('MAIL_MAILER')}} *</label>
                                    <p class="text-muted">{{tr('MAIL_MAILER_note')}}</p>
                                    <input type="text" class="form-control" id="MAIL_MAILER" name="MAIL_MAILER" placeholder="Enter {{tr('MAIL_MAILER')}}" value="{{old('MAIL_MAILER') ?: $env_values['MAIL_MAILER'] }}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_HOST">{{tr('MAIL_HOST')}} *</label>
                                <p class="text-muted">{{tr('mail_host_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_HOST" name="MAIL_HOST" placeholder="Enter {{tr('MAIL_HOST')}}" value="{{old('MAIL_HOST') ?: $env_values['MAIL_HOST']}}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_FROM_ADDRESS">{{tr('MAIL_FROM_ADDRESS')}} *</label>

                                <p class="text-muted">{{tr('MAIL_FROM_ADDRESS_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS" placeholder="Enter {{tr('MAIL_FROM_ADDRESS')}}" value="{{old('MAIL_FROM_ADDRESS') ?: $env_values['MAIL_FROM_ADDRESS']}}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_PORT">{{tr('MAIL_PORT')}} *</label>

                                <p class="text-muted">{{tr('mail_port_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_PORT" name="MAIL_PORT" placeholder="Enter {{tr('MAIL_PORT')}}" value="{{old('MAIL_PORT') ?: $env_values['MAIL_PORT']}}">
                            </div>


                            <div class="form-group">
                                <label for="MAILGUN_DOMAIN">{{ tr('MAILGUN_PUBLIC_KEY') }}({{tr('optional')}})</label>
                                <input type="text" class="form-control" value="{{ old('MAILGUN_PUBLIC_KEY') ?: ($env_values['MAILGUN_PUBLIC_KEY'] ?? '' )  }}" name="MAILGUN_PUBLIC_KEY" id="MAILGUN_PUBLIC_KEY" placeholder="{{ tr('MAILGUN_PUBLIC_KEY') }}">
                            </div>

                            <div class="form-group">
                                <label for="MAILGUN_DOMAIN">{{ tr('MAILGUN_DOMAIN') }}({{tr('optional')}})</label>
                                <input type="text" class="form-control" value="{{ old('MAILGUN_DOMAIN') ?: ($env_values['MAILGUN_DOMAIN'] ?? '')  }}" name="MAILGUN_DOMAIN" id="MAILGUN_DOMAIN" placeholder="{{ tr('MAILGUN_DOMAIN') }}">
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="MAIL_USERNAME">{{tr('MAIL_USERNAME')}} *</label>

                                <p class="text-muted">{{tr('mail_username_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME" placeholder="Enter {{tr('MAIL_USERNAME')}}" value="{{old('MAIL_USERNAME') ?: $env_values['MAIL_USERNAME']}}">
                            </div>

                            <div class="form-group">

                                <label for="MAIL_PASSWORD">{{tr('MAIL_PASSWORD')}} *</label>

                                <p class="text-muted" style="visibility: hidden;">{{tr('mail_username_note')}}</p>

                                <input type="password" class="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD" placeholder="Enter {{tr('MAIL_PASSWORD')}}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_FROM_NAME">{{tr('MAIL_FROM_NAME')}} *</label>

                                <p class="text-muted">{{tr('MAIL_FROM_NAME_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_FROM_NAME" name="MAIL_FROM_NAME" placeholder="Enter {{tr('MAIL_FROM_NAME')}}" value="{{old('MAIL_FROM_NAME') ?: $env_values['MAIL_FROM_NAME']}}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_ENCRYPTION">{{tr('MAIL_ENCRYPTION')}} *</label>

                                <p class="text-muted">{{tr('mail_encryption_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION" placeholder="Enter {{tr('MAIL_ENCRYPTION')}}" value="{{old('MAIL_ENCRYPTION') ?: $env_values['MAIL_ENCRYPTION']}}">
                            </div>
                            <!--
                            <div class="form-group">
                                    <label for="MAILGUN_SECRET">{{ tr('MAILGUN_SECRET') }}</label>
                                    <input type="text" class="form-control" name="MAILGUN_SECRET" id="MAILGUN_SECRET" placeholder="{{ tr('MAILGUN_SECRET') }}" value="{{old('MAILGUN_SECRET') ?: ($env_values['MAILGUN_SECRET'] ?? '') }}"
                            </div>-->
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
       
            <br>
       
        </div>          

        <!-- Social Settings  -->
        <div class="fx-tab-content">
           
           <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf

                <div class="box-body">
                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('social_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="facebook_link">{{tr('facebook_link')}} </label>

                                <input type="text" class="form-control" id="facebook_link" name="facebook_link" placeholder="Enter {{tr('facebook_link')}}" value="{{old('facebook_link') ?: Setting::get('facebook_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="linkedin_link">{{tr('linkedin_link')}} </label>

                                <input type="text" class="form-control" id="linkedin_link" name="linkedin_link" placeholder="Enter {{tr('linkedin_link')}}" value="{{old('linkedin_link') ?: Setting::get('linkedin_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="twitter_link">{{tr('twitter_link')}} </label>

                                    <input type="text" class="form-control" id="twitter_link" name="twitter_link" placeholder="Enter {{tr('twitter_link')}}" value="{{old('twitter_link') ?: Setting::get('twitter_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pinterest_link">{{tr('pinterest_link')}} </label>

                                <input type="text" class="form-control" id="pinterest_link" name="pinterest_link" placeholder="Enter {{tr('pinterest_link')}}" value="{{old('pinterest_link') ?: Setting::get('pinterest_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="instagram_link">{{tr('instagram_link')}} </label>

                                <input type="text" class="form-control" id="instagram_link" name="instagram_link" placeholder="Enter {{tr('instagram_link')}}" value="{{old('instagram_link') ?: Setting::get('instagram_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="youtube_link">{{tr('youtube_link')}} </label>

                                <input type="text" class="form-control" id="youtube_link" name="youtube_link" placeholder="Enter {{tr('youtube_link')}}" value="{{old('youtube_link') ?: Setting::get('youtube_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="snapchat_link">{{tr('snapchat_link')}} </label>

                                <input type="text" class="form-control" id="snapchat_link" name="snapchat_link" placeholder="Enter {{tr('snapchat_link')}}" value="{{old('snapchat_link') ?: Setting::get('snapchat_link')}}">
                            </div>
                        </div>
                        
                        <div class="clearfix"></div>
                        
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
        
            <br>
        
        </div>

        <!--Social login-->
        <div class="fx-tab-content">
           
           <form id="social_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf

                <div class="box-body">

                    <div class="row">

                         <div class="col-md-12">

                           <h5 class="settings-sub-header text-uppercase" ><b>{{tr('social_login')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-md-12">
                            <h5 class="settings-sub-header text-uppercase text-danger"><b>{{tr('fb_settings')}}</b></h5>
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="FB_CLIENT_ID">{{tr('FB_CLIENT_ID')}} *</label>

                                <input type="text" class="form-control" name="FB_CLIENT_ID" id="FB_CLIENT_ID" placeholder="Enter {{tr('FB_CLIENT_ID')}}" value="{{old('FB_CLIENT_ID') ?: Setting::get('FB_CLIENT_ID') }}">
                            </div>
                        </div>
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="FB_CLIENT_SECRET">{{tr('FB_CLIENT_SECRET')}} *</label>

                                <input type="text" class="form-control" name="FB_CLIENT_SECRET" id="FB_CLIENT_SECRET" placeholder="Enter {{tr('FB_CLIENT_SECRET')}}" value="{{old('FB_CLIENT_SECRET') ?: Setting::get('FB_CLIENT_SECRET') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="FB_CALL_BACK">{{tr('FB_CALL_BACK')}} *</label>

                                <input type="text" class="form-control" name="FB_CALL_BACK" id="FB_CALL_BACK" placeholder="Enter {{tr('FB_CALL_BACK')}}" value="{{old('FB_CALL_BACK') ?: Setting::get('FB_CALL_BACK') }}">
                            </div>
                        </div>

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase text-danger"><b>{{tr('google_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="GOOGLE_CLIENT_ID">{{tr('GOOGLE_CLIENT_ID')}} *</label>

                                <input type="text" class="form-control" name="GOOGLE_CLIENT_ID" id="GOOGLE_CLIENT_ID" placeholder="Enter {{tr('GOOGLE_CLIENT_ID')}}" value="{{old('GOOGLE_CLIENT_ID') ?: Setting::get('GOOGLE_CLIENT_ID') }}">
                            </div>
                        </div>
                       
                         <div class="col-md-6">
                            <div class="form-group">
                                <label for="GOOGLE_CLIENT_SECRET">{{tr('GOOGLE_CLIENT_SECRET')}} *</label>

                                <input type="text" class="form-control" name="GOOGLE_CLIENT_SECRET" id="GOOGLE_CLIENT_SECRET" placeholder="Enter {{tr('GOOGLE_CLIENT_SECRET')}}" value="{{old('GOOGLE_CLIENT_SECRET') ?: Setting::get('GOOGLE_CLIENT_SECRET') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="GOOGLE_CALL_BACK">{{tr('GOOGLE_CALL_BACK')}} *</label>

                                <input type="text" class="form-control" name="GOOGLE_CALL_BACK" id="GOOGLE_CALL_BACK" placeholder="Enter {{tr('GOOGLE_CALL_BACK')}}" value="{{old('GOOGLE_CALL_BACK') ?: Setting::get('GOOGLE_CALL_BACK') }}">
                            </div>
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
        
            <br>
        
        </div>

        <!--Notification settings -->
        <div class="fx-tab-content">
           
           <form id="social_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body">
                
                    <div class="row">

                        <div class="col-md-12">
                            <h5 class="settings-sub-header text-uppercase"><b>{{tr('notification_settings')}}</b></h5>
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="user_fcm_sender_id">{{ tr('user_fcm_sender_id') }}</label>

                                <input type="text" class="form-control" name="FCM_SENDER_ID" id="FCM_SENDER_ID"
                                value="{{old('FCM_SENDER_ID') ?: envfile('FCM_SENDER_ID') }}" placeholder="{{ tr('user_fcm_sender_id') }}">
                            </div>
                        </div>  

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="user_fcm_server_key">{{ tr('user_fcm_server_key') }}</label>

                                <input type="text" class="form-control" name="FCM_SERVER_KEY" id="FCM_SERVER_KEY"
                                value="{{old('FCM_SERVER_KEY') ?: envfile('FCM_SERVER_KEY') }}" placeholder="{{ tr('user_fcm_server_key') }}">
                            </div>
                        </div> 

                    </div>  
        
                </div> 

                <div class="form-actions">

                    <div class="pull-right">
                    
                        <button type="reset" class="btn btn-warning mr-1">
                            <i class="ft-x"></i> {{ tr('reset') }} 
                        </button>

                        <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                    
                    </div>

                    <div class="clearfix"></div>

                </div>
            
            </form>
            <br>

        </div>

        <div class="fx-tab-content">
           
           <form id="live_video_settings_save" enctype="multipart/form-data" role="form" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf

                <div class="box-body">

                    <div class="row">

                         <div class="col-md-12">

                           <h5 class="settings-sub-header text-uppercase" ><b>{{tr('live_video_settings')}}</b></h5>

                            <hr>

                        </div>

                        @if(Setting::get('is_agora_configured'))
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="AGORA_APP_ID">{{tr('AGORA_APP_ID')}} *</label>
                                <input type="text" class="form-control" name="agora_app_id" id="agora_app_id" placeholder="Enter {{tr('AGORA_APP_ID')}}" value="{{old('agora_app_id') ?: Setting::get('agora_app_id') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="AGORA_CERTIFICATE_ID">{{tr('AGORA_CERTIFICATE_ID')}} *</label>
                                <input type="text" class="form-control" name="agora_certificate_id" id="agora_certificate_id" placeholder="Enter {{tr('AGORA_CERTIFICATE_ID')}}" value="{{old('agora_certificate_id') ?: Setting::get('agora_certificate_id') }}">
                            </div>
                        </div>
                        @endif


                      
                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="live_streaming_placeholder_img">{{tr('LIVE_STREAMING_PLACEHOLDER_IMG')}} *</label>
                                
                                <input type="file" class="form-control" id="live_streaming_placeholder_img" name="live_streaming_placeholder_img" accept="image/png,image/jpeg" placeholder="{{tr('LIVE_STREAMING_PLACEHOLDER_IMG')}}">
                                
                                <p class="txt-warning">{{tr('png_image_note')}}</p>

                            </div>
                            @if(Setting::get('live_streaming_placeholder_img'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('live_streaming_placeholder_img')}}" alt="{{Setting::get('site_name')}}"> 

                            @endif
                        </div>
                            
                        

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="LIVE_STREAMING_ADMIN_COMMISSION">{{tr('LIVE_STREAMING_ADMIN_COMMISSION')}} *</label>
                                <input type="text" class="form-control" name="live_streaming_admin_commission" id="live_streaming_admin_commission" placeholder="Enter {{tr('LIVE_STREAMING_ADMIN_COMMISSION')}}" value="{{old('live_streaming_admin_commission') ?: Setting::get('live_streaming_admin_commission') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_token_call_charge">{{tr('min_token_call_charge')}} *</label>
                                <input type="number" class="form-control" name="min_token_call_charge" id="min_token_call_charge" placeholder="Enter {{tr('min_token_call_charge')}}" value="{{old('min_token_call_charge') ?: Setting::get('min_token_call_charge') }}">
                            </div>
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
        
            <br>
        
        </div>
        <div class="fx-tab-content">
           
           <form id="image_settings_save" enctype="multipart/form-data" role="form" action="{{route('admin.settings_placeholder_img.save')}}" method="POST">
                
                @csrf

                <div class="box-body">

                    <div class="row">

                         <div class="col-md-12">

                           <h5 class="settings-sub-header text-uppercase" ><b>{{tr('image_settings')}}</b></h5>

                            <hr>

                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="profile_placeholder">{{tr('profile_placeholder')}}</label>
                                
                                <input type="file" class="form-control" id="profile_placeholder" name="profile_placeholder" accept="image/png,image/jpeg,image/jpg" placeholder="{{tr('profile_placeholder')}}">

                            </div>
                            @if(Setting::get('profile_placeholder'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('profile_placeholder')}}" alt="profile_placeholder"> 

                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="cover_placeholder">{{tr('cover_placeholder')}}</label>
                                
                                <input type="file" class="form-control" id="cover_placeholder" name="cover_placeholder" accept="image/png,image/jpeg,image/jpg" placeholder="{{tr('cover_placeholder')}}">

                            </div>
                            @if(Setting::get('cover_placeholder'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('cover_placeholder')}}" alt="cover_placeholder"> 

                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="post_image_placeholder">{{tr('post_image_placeholder')}}</label>
                                
                                <input type="file" class="form-control" id="post_image_placeholder" name="post_image_placeholder" accept="image/png,image/jpeg,image/jpg" placeholder="{{tr('post_image_placeholder')}}">

                            </div>
                            @if(Setting::get('post_image_placeholder'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('post_image_placeholder')}}" alt="post_image_placeholder"> 

                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="post_video_placeholder">{{tr('post_video_placeholder')}}</label>
                                
                                <input type="file" class="form-control" id="post_video_placeholder" name="post_video_placeholder" accept="image/png,image/jpeg,image/jpg" placeholder="{{tr('post_video_placeholder')}}">

                            </div>
                            @if(Setting::get('post_video_placeholder'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('post_video_placeholder')}}" alt="post_video_placeholder"> 

                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="video_call_placeholder">{{tr('video_call_placeholder')}}</label>
                                
                                <input type="file" class="form-control" id="video_call_placeholder" name="video_call_placeholder" accept="image/png,image/jpeg,image/jpg" placeholder="{{tr('video_call_placeholder')}}">

                            </div>
                            @if(Setting::get('video_call_placeholder'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('video_call_placeholder')}}" alt="video_call_placeholder"> 

                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="audio_call_placeholder">{{tr('audio_call_placeholder')}}</label>
                                
                                <input type="file" class="form-control" id="audio_call_placeholder" name="audio_call_placeholder" accept="image/png,image/jpeg,image/jpg" placeholder="{{tr('audio_call_placeholder')}}">

                            </div>
                            @if(Setting::get('audio_call_placeholder'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('audio_call_placeholder')}}" alt="audio_call_placeholder"> 

                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="ppv_image_placeholder">{{tr('ppv_image_placeholder')}}</label>
                                
                                <input type="file" class="form-control" id="ppv_image_placeholder" name="ppv_image_placeholder" accept="image/png,image/jpeg,image/jpg" placeholder="{{tr('ppv_image_placeholder')}}">

                            </div>
                            @if(Setting::get('ppv_image_placeholder'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('ppv_image_placeholder')}}" alt="ppv_image_placeholder"> 

                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="ppv_audio_placeholder">{{tr('ppv_audio_placeholder')}}</label>
                                
                                <input type="file" class="form-control" id="ppv_audio_placeholder" name="ppv_audio_placeholder" accept="image/png,image/jpeg,image/jpg" placeholder="{{tr('ppv_audio_placeholder')}}">

                            </div>
                            @if(Setting::get('ppv_audio_placeholder'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('ppv_audio_placeholder')}}" alt="ppv_audio_placeholder"> 

                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label for="ppv_video_placeholder">{{tr('ppv_video_placeholder')}}</label>
                                
                                <input type="file" class="form-control" id="ppv_video_placeholder" name="ppv_video_placeholder" accept="image/png,image/jpeg,image/jpg" placeholder="{{tr('ppv_video_placeholder')}}">

                            </div>
                            @if(Setting::get('ppv_video_placeholder'))

                                <img class="img img-thumbnail m-b-20" style="width: 50%" src="{{Setting::get('ppv_video_placeholder')}}" alt="ppv_video_placeholder"> 

                            @endif
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
        
            <br>
        
        </div>

        <!-- APP Url Settings -->
        <div class="fx-tab-content">
            
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body">
                        
                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('mobile_settings')}}</b></h5>

                            <hr>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="playstore_user">{{tr('playstore_user')}} *</label>
                                <input type="text" class="form-control" id="playstore_user" name="playstore_user" placeholder="Enter {{tr('playstore_user')}}" value="{{old('playstore_user') ?: Setting::get('playstore_user')}}">
                            </div>

                        </div>

                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="appstore_user">{{tr('appstore_user')}} *</label>

                                <input type="text" class="form-control" id="appstore_user" name="appstore_user" placeholder="Enter {{tr('appstore_user')}}" value="{{old('appstore_user') ?: Setting::get('appstore_user')}}">
                            </div>
                        </div>                       
                        
                    </div>

                </div>

                <div class="box-footer">

                    <button type="reset" class="btn btn-warning">{{tr('reset')}}</button>

                    @if(Setting::get('admin_delete_control') == 1)
                        <button type="submit" class="btn btn-primary pull-right" disabled>{{tr('submit')}}</button>
                    @else
                        <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                    @endif
       
                </div>
       
            </form>
       
            <br>
       
        </div>

        <!-- Contact Information -->
        <div class="fx-tab-content">
            
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body">
                        
                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('contact_information')}}</b></h5>

                            <hr>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="copyright_content">{{tr('copyright_content')}} </label>
                                <input type="text" class="form-control" id="copyright_content" name="copyright_content" placeholder="Enter {{tr('copyright_content')}}" value="{{old('copyright_content') ?: Setting::get('copyright_content')}}">
                            </div>

                        </div>

                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="contact_mobile">{{tr('contact_mobile')}} </label>

                                <input type="text" class="form-control" id="contact_mobile" name="contact_mobile" placeholder="Enter {{tr('contact_mobile')}}" value="{{old('contact_mobile') ?: Setting::get('contact_mobile')}}">
                            </div>
                        </div>

                        <div class="col-md-6">

                           <div class="form-group">
                                <label for="contact_email">{{tr('contact_email')}} </label>

                                <input type="text" class="form-control" id="contact_email" name="contact_email" placeholder="Enter {{tr('contact_email')}}" value="{{old('contact_email') ?: Setting::get('contact_email')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_address">{{tr('contact_address')}} </label>

                                <input type="text" class="form-control" id="contact_address" name="contact_address" placeholder="Enter {{tr('contact_address')}}" value="{{old('contact_address') ?: Setting::get('contact_address')}}">
                            </div>
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
       
            <br>
       
        </div>

        <div class="fx-tab-content">
        
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body"> 
                    <div class="row"> 

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('configuration_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-lg-9">

                            <div class="form-group">
                                <label for="google_analytics">{{tr('chat_socket_url')}}</label>
                                <input class="form-control" id="chat_socket_url" name="chat_socket_url" value="{{old('chat_socket_url') ?: (Setting::get('chat_socket_url') ?? '')}}">
                            </div>

                        </div> 


                    </div>
                </div>
                <!-- /.box-body -->

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
        
            <br>
        
        </div>

        <!-- OTHER Settings -->

        <div class="fx-tab-content">
        
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body"> 
                    <div class="row"> 

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('other_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-lg-12">

                            <div class="form-group">
                                <label for="google_analytics">{{tr('google_analytics')}}</label>
                                <textarea class="form-control" id="google_analytics" name="google_analytics">{{Setting::get('google_analytics')}}</textarea>
                            </div>

                        </div> 

                        <div class="col-lg-12">

                            <div class="form-group">
                                <label for="header_scripts">{{tr('header_scripts')}}</label>
                                <textarea class="form-control" id="header_scripts" name="header_scripts">{{Setting::get('header_scripts')}}</textarea>
                            </div>

                        </div> 

                        <div class="col-lg-12">

                            <div class="form-group">
                                <label for="body_scripts">{{tr('body_scripts')}}</label>
                                <textarea class="form-control" id="body_scripts" name="body_scripts">{{Setting::get('body_scripts')}}</textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.box-body -->

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
        
            <br>
        
        </div>


        <!-- Watermark Settings -->

        <div class="fx-tab-content">
            
        @if(Setting::get('is_watermark_logo_enabled'))

        <div class="fansclub-tab-content">
        
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body"> 
                    <div class="row"> 

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('watermark_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-lg-12">

                            <div class="form-group">
                                <label for="watermark_logo">{{tr('watermark_logo')}} *</label>
                                <p class="txt-warning">{{tr('upload_image_square')}}</p>
                                <p class="txt-warning">{{tr('png_image_note')}}</p>
                                <input type="file" class="form-control" id="watermark_logo" name="watermark_logo" accept="image/png" placeholder="{{tr('watermark_logo')}}">
                            </div>

                            @if(Setting::get('watermark_logo'))

                                <img class="img img-thumbnail m-b-20" style="width: 40%" src="{{Setting::get('watermark_logo')}}" alt="{{Setting::get('site_name')}}"> 

                            @endif
                        
                        </div> 

                        <div class="col-md-12">

                            <div class="form-group">

                                <label for="site_icon">{{tr('watermark_position')}} </label>

                                <select class="form-control select2" id="watermark_position" name="watermark_position">
                                    <option value="">{{tr('select_position')}}</option>

                                    <option value="{{WATERMARK_TOP_LEFT}}" @if(Setting::get('watermark_position') == WATERMARK_TOP_LEFT) selected @endif> {{tr('top_left')}}</option>

                                    <option value="{{WATERMARK_TOP_RIGHT}}" @if(Setting::get('watermark_position') == WATERMARK_TOP_RIGHT) selected @endif>{{tr('top_right')}}</option>

                                    <option value="{{WATERMARK_BOTTOM_LEFT}}" @if(Setting::get('watermark_position') == WATERMARK_BOTTOM_LEFT) selected @endif>{{tr('bottom_left')}}</option>

                                    <option value="{{WATERMARK_BOTTOM_RIGHT}}" @if(Setting::get('watermark_position') == WATERMARK_BOTTOM_RIGHT) selected @endif>{{tr('bottom_right')}}</option>

                                    <option value="{{WATERMARK_CENTER}}" @if(Setting::get('watermark_position') == WATERMARK_CENTER) selected @endif>{{tr('center')}}</option>

                                </select>
                            </div>

                        </div> 

                    </div>
                </div>
                <!-- /.box-body -->

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
        
            <br>
        
        </div>

        @endif


        
    </div>

</div>

</section>

@endsection


@section('scripts')

<script type="text/javascript">
    
    $(document).ready(function() {
        $("div.fx-tab-menu>div.list-group>a").click(function(e) {
            e.preventDefault();
            $(this).siblings('a.active').removeClass("active");
            $(this).addClass("active");
            var index = $(this).index();
            $("div.fx-tab>div.fx-tab-content").removeClass("active");
            $("div.fx-tab>div.fx-tab-content").eq(index).addClass("active");
        });
    });

    function checkPaypalType(val){
        console.log(val);
        $("#paypal_live_key").hide();
        $("#paypal_sandbox_key").show();
        if(val == 'live') {
            $("#paypal_live_key").show();
            $("#paypal_sandbox_key").hide();
        } else {
          $("#paypal_live_key").hide();
          $("#paypal_sandbox_key").show();
        }
    }

    function setCurrency(id) {

        let currency_value = $("#"+id).data("currency");

        let currency = document.getElementById("currency");

        currency.value = currency_value;
    }
</script>
@endsection