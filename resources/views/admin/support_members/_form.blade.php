<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{ $support_member->id ? tr('edit_support_member') : tr('add_support_member') }}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.support_members.index') }}" class="btn btn-primary"><i class="ft-eye icon-left"></i>{{ tr('view_support_members') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.support_members.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body"> 

                                <div class="row">

                                    <input type="hidden" name="support_member_id" id="support_member_id" value="{{ $support_member->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="support_member_name">{{ tr('first_name') }}*</label>
                                            <input type="text" id="first_name" name="first_name" class="form-control" placeholder="{{ tr('first_name') }}" value="{{ $support_member->first_name ?: old('first_name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="support_member_name">{{ tr('last_name') }}*</label>
                                            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="{{ tr('last_name') }}" value="{{ $support_member->last_name ?: old('last_name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                    

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">{{tr('email')}}*</label>
                                            <input type="email" id="email" name="email" class="form-control" placeholder="E-mail" value="{{ $support_member->email ?: old('email') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>{{ tr('select_picture') }}</label>

                                            <input type="file" class="form-control" name="picture" accept="image/*" >
                                                                      
                                        </div>

                                    </div>

                                </div>

                                @if(!$support_member->id)
                                
                                <div class="row">

                                    <div class="col-md-6">                    
                                        <div class="form-group">
                                            <label for="password" class="">{{ tr('password') }} *</label>
                                            <input type="password" minlength="6" required name="password" class="form-control" id="password" placeholder="{{ tr('password') }}" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">  
                                            <label for="confirm-password" class="">{{ tr('confirm_password') }} *</label>
                                          <input type="password" minlength="6" required name="password_confirmation" class="form-control" id="confirm-password" placeholder="{{ tr('confirm_password') }}">
                                        </div>
                                    </div>
                                
                                </div>

                                @endif

                                
                                
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

