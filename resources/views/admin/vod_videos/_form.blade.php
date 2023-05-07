<section class="content">
    
            <div class="box">    
                <div class="box-header with-border">

                    <h3 class="box-title">{{ $vod_video->id ? tr('edit_vod') : tr('create_vod') }}</h3>
                    <h6 class="box-subtitle"></h6>

                    <div class="box-tools pull-right">
                        <a href="{{route('admin.vod_videos.index') }}" class="btn btn-primary"><i class="ft-eye icon-left"></i>{{ tr('view_vod') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.vod_videos.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">


                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="page">
                                                {{tr('user_name')}}
                                                <span class="required" aria-required="true"> <span class="admin-required">*</span> </span>
                                            </label>

                                            <select class="form-control select2" name="user_id" required>

                                                <option>{{tr('select_user_name')}}</option>
                                                @foreach($users as $user)
                                                    <option value="{{$user->id}}" @if($user->id == $vod_video->user_id) selected="true" @endif>
                                                        {{$user->name}}
                                                    </option>
                                                @endforeach
                                            
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 preview_file">
                                        
                                        <div class="form-group">
                                            <label for="page">{{tr('preview_file')}}</label>
                                            <input type="file" class="form-control" name="preview_file" accept="image/*" />
                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    

                                   <div class="col-md-6">
                                        
                                        <div class="form-group">
                                            <label for="page">{{tr('upload_video')}}</label>
                                             <span class="required" aria-required="true"> <span class="admin-required">*</span>
                                            <input type="file" class="form-control" name="vod_files" required accept="video/*" />
                                        </div>

                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="page">
                                                {{tr('post_category')}}
                                                <span class="required" aria-required="true"> <span class="admin-required">*</span> </span>
                                            </label>

                                            <select multiple="multiple" class="form-control select2" name="post_category_ids[]" required>

                                                <option>{{tr('select_post_category')}}</option>
                                                @foreach($post_categories as $post_category)
                                                    <option value="{{$post_category->id}}" @if(in_array($post_category->id,$vod_category_details)) selected="true" @endif>
                                                        {{$post_category->name}}
                                                    </option>
                                                @endforeach
                                            
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <input type="hidden" name="vod_id" id="vod_id" value="{{ $vod_video->id}}">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('description') }}</label>
                                            <textarea name="description" class="form-control">{{ $vod_video->description ?: old('description') }}</textarea>
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

</section>

