@extends('layouts.admin') 

@section('title', tr('features_control')) 

@section('content-header', tr('features_control')) 

@section('breadcrumb')

<li class="breadcrumb-item active">{{ tr('features_control') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-lg-12">

            <div class="card">

               <div class="card-header border-bottom border-gray">
                <h4 class="card-title" id="basic-layout-form">{{tr('features_control')}}</h4>
            </div>

            <div class="card-content collapse show">

                <div class="card-body">

                    <form class="forms-sample" action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                        @csrf

                        <div class="card-body">

                            <div class="row">

                                <div class="form-group col-md-6">

                                    <label>{{ tr('notification_count_update_enabled') }}</label>
                                    <br>
                                        <input required type="radio" name="is_notification_count_enabled" value="1" class="with-gap" id="is_notification_count_enabled_yes"  @if(Setting::get('is_notification_count_enabled') == 1) checked @endif>
                                        <label for="is_notification_count_enabled_yes">
                                           {{tr('yes')}}
                                        </label>

                                    
                                        <input required type="radio" name="is_notification_count_enabled" class="flat-red"  id="is_notification_count_enabled_no" value="0" @if(Setting::get('is_notification_count_enabled') == 0) checked @endif>
                                        
                                        <label for="is_notification_count_enabled_no">
                                            {{tr('no')}}
                                        </label>
                                    </div>

                                <div class="form-group col-md-6">

                                    <label>{{ tr('s3_bucket') }}</label>
                                    <br>
                                   
                                        <input required type="radio" name="s3_bucket" id="s3_bucket_enable" value="1" class="flat-red" @if(Setting::get('s3_bucket') == 1) checked @endif>
                                       
                                        <label for="s3_bucket_enable">
                                           {{tr('enable')}}
                                        </label>

                                   
                                        <input required type="radio" name="s3_bucket" id="s3_bucket_disable" class="flat-red"  value="0" @if(Setting::get('s3_bucket') == 0) checked @endif>

                                        <label for="s3_bucket_disable">
                                           {{tr('disable')}}
                                        </label>

                                </div>

                                <div class="form-group col-md-6">

                                    <label>{{ tr('is_multilanguage_support') }}</label>
                                    <br>
                                  
                                        <input required type="radio" name="is_multilanguage_enabled" id="is_multilanguage_enabled_yes" value="1" class="flat-red" @if(Setting::get('is_multilanguage_enabled') == 1) checked @endif>
                                       
                                         <label for="is_multilanguage_enabled_yes">
                                            {{tr('yes')}}
                                        </label>

                                    
                                        <input required type="radio" name="is_multilanguage_enabled" id="is_multilanguage_enabled_no"class="flat-red"  value="0" @if(Setting::get('is_multilanguage_enabled') == 0) checked @endif>
                                        
                                         <label for="is_multilanguage_enabled_no">
                                           {{tr('no')}}
                                        </label>

                                </div>


                                <div class="form-group col-md-6">

                                    <label>{{ tr('is_watermark_logo_enabled') }}</label>
                                    <br>
                                    
                                       <input required type="radio" name="is_watermark_logo_enabled" id="is_watermark_logo_enabled_yes" value="1" class="flat-red" @if(Setting::get('is_watermark_logo_enabled') == 1) checked @endif>
                                       
                                        <label for="is_watermark_logo_enabled_yes">
                                           {{tr('yes')}}
                                        </label>
                                  
                                       <input required type="radio" name="is_watermark_logo_enabled" id="is_watermark_logo_enabled_no" class="flat-red"  value="0" @if(Setting::get('is_watermark_logo_enabled') == 0) checked @endif>
                                      
                                        <label for="is_watermark_logo_enabled_no">
                                           {{tr('no')}}
                                        </label>

                               </div>


                               <div class="form-group col-md-6">

                                <label>{{ tr('paypal_payment_status') }}</label>
                                <br>
                               
                                    <input required type="radio" name="is_paypal_enabled" id="is_paypal_enabled_yes" value="1" class="flat-red" @if(Setting::get('is_paypal_enabled') == 1) checked @endif>
                                    
                                    <label for="is_paypal_enabled_yes">
                                        {{tr('enable')}}
                                    </label>
                               
                                    <input required type="radio" name="is_paypal_enabled" id="is_paypal_enabled_no" class="flat-red"  value="0" @if(Setting::get('is_paypal_enabled') == 0) checked @endif>
                                    
                                    <label for="is_paypal_enabled_no">
                                         {{tr('disable')}}
                                    </label>

                            </div>

                            <div class="form-group col-md-6">

                                <label>{{ tr('is_user_active_status') }}</label>
                                <br>
                                
                                    <input required type="radio" name="is_user_active_status" id="is_user_active_status_yes" value="1" class="flat-red" @if(Setting::get('is_user_active_status') == 1) checked @endif>
                                    
                                    <label for="is_user_active_status_yes">
                                        {{tr('enable')}}
                                    </label>
                                    
                                    <input required type="radio" name="is_user_active_status" id="is_user_active_status_no" class="flat-red"  value="0" @if(Setting::get('is_user_active_status') == 0) checked @endif>
                                   
                                    <label for="is_user_active_status_no">
                                       {{tr('disable')}}
                                    </label>
                                

                            </div>

                            <div class="form-group col-md-6">

                                <label>{{ tr('is_referral_enabled') }}</label>
                                <br>
                                
                                    <input required type="radio" name="is_referral_enabled" id="is_referral_enabled_yes" value="1" class="flat-red" @if(Setting::get('is_referral_enabled') == 1) checked @endif>
                                  
                                     <label for="is_referral_enabled_yes">
                                        {{tr('enable')}}
                                    </label>

                                
                                    <input required type="radio" name="is_referral_enabled" id="is_referral_enabled_no" class="flat-red"  value="0" @if(Setting::get('is_referral_enabled') == 0) checked @endif>
                                    
                                     <label for="is_referral_enabled_no">
                                        {{tr('disable')}}
                                    </label>

                            </div>

                            <div class="form-group col-md-6">

                                <label>{{ tr('is_chat_asset_feature_enabled') }}</label>
                                <br>
                                
                                   <input required type="radio" name="is_chat_asset_enabled" id="is_chat_asset_enabled_yes" value="1" class="flat-red" @if(Setting::get('is_chat_asset_enabled') == 1) checked @endif>
                                 
                                    <label for="is_chat_asset_enabled_yes">
                                         {{tr('yes')}}
                                    </label>

                               
                                   <input required type="radio" name="is_chat_asset_enabled" id="is_chat_asset_enabled_no" class="flat-red"  value="0" @if(Setting::get('is_chat_asset_enabled') == 0) checked @endif>
                                   
                                    <label for="is_chat_asset_enabled_no">
                                        {{tr('no')}}
                                    </label>

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

        </div>

    </div>

</div>

</div>

</div> 


</section>

@endsection 