<section class="content">
    
    <div class="box">    
        <div class="box-header with-border">

            <h3 class="box-title">{{ $story->id ? tr('edit_story') : tr('create_story') }}</h3>
            <h6 class="box-subtitle"></h6>

            <div class="box-tools pull-right">
                <a href="{{route('admin.stories.index') }}" class="btn btn-primary"><i class="ft-eye icon-left"></i>{{ tr('view_story') }}</a>
            </div>

        </div>

        <div class="card-content collapse show">

            <div class="card-body">
            
                <div class="card-text">

                </div>

                <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.stories.save') }}" method="POST" enctype="multipart/form-data" role="form">
                   
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

                                        <option value="">{{tr('select_user_name')}}</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}" @if($user->id == $story->user_id) selected="true" @endif>
                                                {{$user->name}}
                                            </option>
                                        @endforeach
                                    
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="page">
                                        {{tr('select_story_type')}}
                                    </label><br>

                                    <input type="radio" class="with-gap" id="{{STORY_IMAGE}}" onclick="select_image_type();" name="file_type" value="{{STORY_IMAGE}}" {{ $story->id ? (($story_file && $story_file->file_type  == STORY_IMAGE) ? "checked" : "") : ""}}>
                                    <label for="{{STORY_IMAGE}}">{{tr('image')}}</label>
                                    <input type="radio" class="with-gap" id="{{STORY_VIDEO}}" onclick="select_image_type();" name="file_type" value="{{STORY_VIDEO}}" {{ $story->id ? (($story_file && $story_file->file_type  == STORY_VIDEO) ? "checked" : "") : ""}}>
                                    <label for="{{STORY_VIDEO}}">{{tr('video')}}</label>
                                  
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 preview_file" style="display: none;">
                                
                                <div class="form-group">
                                    <label for="page">{{tr('preview_file')}}</label>
                                    <p class="text-muted">{{tr('preview_story_file_notes')}}</p>

                                    <input type="file" class="form-control" name="preview_file" accept="image/png,image/jpeg,image/jpg" />
                                </div>

                            </div>

                           <div class="col-md-6">
                                
                                <div class="form-group">
                                    <label for="page">{{tr('upload_files')}}</label>
                                    <span class="required" aria-required="true"> <span class="admin-required">*</span> </span>
                                    <p class="text-muted">{{tr('story_file_notes')}}</p>

                                    <input type="file" class="form-control" id="upload" name="story_files" accept="image/*,video/*" />
                                </div>

                            </div>

                        </div>

                        <div class="row" style="display: none;">

                            <input type="hidden" name="story_id" id="story_id" value="{{$story->id}}">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_name">{{ tr('content') }}*</label>
                                    <textarea rows="5" name="content" class="form-control">{{ $story->content ?: old('content') }}</textarea>
                                </div>
                            </div>

                        </div>

                        <!-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_name">{{ tr('amount') }}</label>
                                    <input type="number" min="0" id="amount" name="amount" class="form-control" placeholder="{{ tr('amount') }}" value="{{ $story->amount ?: old('amount') }}" >
                                </div>
                            </div>

                        </div> -->
                        
                        
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

