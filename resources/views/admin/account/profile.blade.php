@extends('layouts.admin') 

@section('title', tr('profile')) 

@section('content-header', tr('profile')) 

@section('breadcrumb')
<li class="breadcrumb-item active">{{tr('profile')}}</li>

@endsection 

@section('content')

<section class="content">

<div id="user-profile">

    <div class="row">

        <div class="col-xl-4 col-lg-4 col-md-12">

            <div class="card">
                
                <div class="card-content">
                    
                    <div class="card-body">

                        <div class="card-body">

                            <center class="m-t-30"> <img src="{{$admin->picture ?: asset('placeholder.png')}}" class="img-circle mb-2" width="150" />

                                <h4 class="card-title m-t-10" style="border-bottom: none;">{{$admin->name ?: tr('n_a')}}</h4>

                                <!-- <h6 class="card-subtitle">{{$admin->timezone}}</h6> -->
                            </center>

                        </div>


                        <ul class="list-group list-group-unbordered">

                            <li class="list-group-item">
                                <b>{{tr('email')}}</b> <a class="pull-right">{{$admin->email ?: tr('n_a')}}</a>
                            </li>
                            
                            <li class="list-group-item">
                                <b>{{tr('about')}}</b> <a class="pull-right">{{$admin->about ?: tr('n_a')}}</a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-8 col-lg-8 col-md-12">

            <div class="card">

                <div class="card-content">

                    <div class="card-body">
                   
                        <ul class="nav nav-tabs nav-top-border no-hover-bg reps-tab-flex">
                            <li class="nav-item">
                                <a class="nav-link active" id="base-tab_update_profile" data-toggle="tab" aria-controls="tab_update_profile" href="#tab_update_profile" aria-expanded="true">{{ tr('update_profile') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="base-tab_upload_image" data-toggle="tab" aria-controls="tab_upload_image" href="#tab_upload_image" aria-expanded="false">{{ tr('upload_image') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="base-tab_change_password" data-toggle="tab" aria-controls="tab_change_password" href="#tab_change_password" aria-expanded="false">{{ tr('change_password') }}</a>
                            </li>
                        </ul>

                        <div class="tab-content pt-1">

                            <div role="tabpanel" class="tab-pane active" id="tab_update_profile" aria-expanded="true" aria-labelledby="base-tab_update_profile">

                                <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.profile.save') }}" method="POST" enctype="multipart/form-data" role="form">
                                    @csrf

                                    <input type="hidden" name="admin_id" value="{{Auth::guard('admin')->user()->id}}">

                                    <div class="form-group">
                                        <label for="name" required class="col-sm-2 control-label">{{ tr('name') }}</label>

                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="name" name="name" value="{{ Auth::guard('admin')->user()->name }}" placeholder="{{ tr('username') }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="col-sm-2 control-label">{{ tr('email') }}</label>

                                        <div class="col-sm-10">
                                            <input type="email" required value="{{ Auth::guard('admin')->user()->email }}" name="email" class="form-control" id="email" placeholder="{{ tr('email') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="about" class="col-sm-2 control-label">{{ tr('about') }}</label>

                                        <div class="col-sm-10">
                                            <input type="text" value="{{ Auth::guard('admin')->user()->about }}" name="about" class="form-control" id=" " placeholder="{{ tr('about') }}">
                                           
                                        </div>
                                    </div>

                                    <div class="form-actions padding-btm-zero">

                                        <button type="reset" class="btn btn-warning mr-1">
                                            <i class="ft-x mr-1"></i> {{ tr('reset') }} 
                                        </button>

                                        <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled @endif ><i class="fa fa-check-square-o mr-1"></i>{{ tr('submit') }}</button>
                                        
                                        <div class="clearfix"></div>

                                    </div>

                                </form>

                            </div>

                            <div class="tab-pane" id="tab_upload_image" aria-labelledby="base-tab_upload_image">
                               
                                <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.profile.save') }}" method="POST" enctype="multipart/form-data" role="form">
                                    @csrf

                                     <input type="hidden" name="admin_id" value="{{Auth::guard('admin')->user()->id}}">

                                    @if(Auth::guard('admin')->user()->picture)
                                        <img style="height: 90px; margin-bottom: 15px; border-radius:2em;" src="{{ Auth::guard('admin')->user()->picture }}"> 
                                    @else
                                        <img style="height: 90px; margin-bottom: 15px; border-radius:2em;" class="profile-user-img img-responsive img-circle" src="{{ asset('placeholder.png') }}">
                                     @endif

                                    <div class="form-group">
                                        <label for="picture" class="col-sm-2 control-label">{{ tr('picture') }}</label>

                                        <div class="col-sm-10">
                                            <input type="file" required accept="image/png,image/jpeg" name="picture" id="picture">
                                            <p class="help-block">{{ tr('image_validate') }}</p>
                                        </div>
                                    </div>

                                    <div class="form-actions">

                                        <button type="reset" class="btn btn-warning mr-1">
                                            <i class="ft-x"></i> {{ tr('reset') }} 
                                        </button>

                                        <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                                        
                                        <div class="clearfix"></div>

                                    </div>

                                </form>

                            </div>

                            <div class="tab-pane" id="tab_change_password" aria-labelledby="base-tab_change_password">

                                <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.change.password') }}" method="POST" enctype="multipart/form-data" role="form">
                                    @csrf

                                    <input type="hidden" name="admin_id" value="{{ Auth::guard('admin')->user()->id }}">

                                    <div class="form-group">
                                        <label for="old_password" class="col-sm-3 control-label">{{ tr('old_password') }}</label>

                                        <div class="col-sm-8">
                                            <input required type="password" class="form-control" name="old_password" id="old_password" placeholder="{{ tr('old_password') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="col-sm-3 control-label">{{ tr('new_password') }}</label>

                                        <div class="col-sm-8">
                                            <input required type="password" class="form-control" name="password" id="password" placeholder="{{ tr('new_password') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm_password" class="col-sm-3 control-label">{{ tr('confirm_password') }}</label>

                                        <div class="col-sm-8">
                                            <input type="password" required class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Enter {{tr('confirm_password')}}">
                                        </div>
                                    </div>

                                    <div class="form-actions">

                                        <button type="reset" class="btn btn-warning mr-1">
                                            <i class="ft-x"></i> {{ tr('reset') }} 
                                        </button>

                                        <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                                        
                                        <div class="clearfix"></div>

                                    </div>

                                </form>

                            </div>


                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    
</div>

</section>

@endsection 

@section('scripts')

<script>
    // input only number
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 45) {
            return false;
        }
        return true;
    }
</script>

@endsection