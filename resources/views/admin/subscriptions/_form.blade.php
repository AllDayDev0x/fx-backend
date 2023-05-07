<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$subscription->id ? tr('edit_subscription') : tr('add_subscription')}}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.subscriptions.index') }}" class="btn btn-primary"><i class="ft-eye icon-left"></i>{{tr('view_subscriptions')}}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        
                        <form class="forms-sample" action="{{ Setting::get('is_demo_control_enabled') == NO ? route('admin.subscriptions.save') : '#'}}" method="POST" enctype="multipart/form-data" role="form">

                            @csrf

                            <div class="card-body">

                                <input type="hidden" name="subscription_id" id="subscription_id" value="{{$subscription->id}}">

                                <div class="row">

                                    <div class="form-group col-md-6">

                                        <label for="title" class="">{{ tr('title') }} <span class="admin-required">*</span></label>

                                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') ?: $subscription->title }}" placeholder="{{ tr('title') }}" required >
                                        
                                    </div>

                                    <div class="form-group col-md-6">

                                        <label for="amount" class="">{{ tr('amount') }} <span class="admin-required">*</span></label>

                                        <input type="number" value="{{ old('amount') ?: $subscription->amount }}" name="amount" class="form-control" id="amount" placeholder="{{ tr('amount') }}" min="0" step="any" required>
                                    </div>
                                    
                                </div>

                                <div class="row">
                                
                                    <div class="form-group col-md-6">

                                        <label for="plan">{{ tr('plan') }} <span class="admin-required">*</span></label>

                                        <input type="number" min="1"  max="30" required name="plan" class="form-control" id="plan" value="{{ old('plan') ?: $subscription->plan }}" title="{{ tr('plan') }}" placeholder="{{ tr('plan') }}">
                                    </div>

                                     <div class="form-group col-md-6">

                                        <label for="plan_type">{{ tr('plan_type') }} <span class="admin-required">*</span></label>

                                        <select class="form-control select2" id="plan_type" name="plan_type" required="">
                                            <option value="">{{tr('select_plan_type')}}</option>

                                            @foreach($subscription_plan_types as $subscription_plan_type)
                                                <option value="{{$subscription_plan_type}}"@if($subscription->plan_type == $subscription_plan_type) selected @endif>
                                                    {{ucfirst($subscription_plan_type)}}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                </div>

                                <div class="row">
                                
                                    <div class="form-group col-md-6">

                                         <div class="form-group clearfix">

                                          <div class="icheck-success d-inline">
                                                <input type="checkbox" id="checkboxSuccess1" name="is_free" value="{{YES}}" @if($subscription->is_free ==  YES) checked="checked" @endif>
                                                <label for="checkboxSuccess1">{{tr('is_free')}}</label>

                                          </div>

                                        </div>

                                    </div>

                                     <div class="form-group col-md-6">

                                        <div class="form-group clearfix">

                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="checkboxSuccess2" name="is_popular"
                                                value="{{YES}}" @if($subscription->is_popular ==  YES) checked="checked" @endif>
                                                <label for="checkboxSuccess2">{{tr('is_popular')}}</label>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="form-group col-md-12">

                                        <label for="simpleMde">{{ tr('description') }}</label>

                                        <textarea class="form-control" id="description" name="description">{{ old('description') ?: $subscription->description}}</textarea>

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
                        
                    </div>
                
                </div>

            </div>
        
        </div>
    
    </div>

</section>
