<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$content_creator_details->id ? tr('edit_content_creator') : tr('add_content_creator')}}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.content_creators.index') }}" class="btn btn-primary"><i class="ft-eye icon-left"></i>{{ tr('view_content_creators') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.content_creators.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="user_id" id="user_id" value="{{ $content_creator_details->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $content_creator_details->name ?: old('name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">{{tr('email')}}*</label>
                                            <input type="email" id="email" name="email" class="form-control" placeholder="{{tr('email')}}" value="{{ $content_creator_details->email ?: old('email') }}" required>
                                        </div>
                                    </div>

                                </div>

                                @if(!$content_creator_details->id)
                                
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

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">

                                        <label>{{ tr('select_picture') }}</label>

                                            <input type="file" id="picture" class="form-control" name="picture" accept="image/png,image/jpeg">
                                           
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
                        
                    </div>
                
                </div>

            </div>
        
        </div>
    
    </div>

</section>

