<section class="content">

    <div class="box">
        <div id="alert-success" class="alert-success col-lg-12" style="display: none;">

            <div class="col-lg-10"  id="alert-success-staging">
            </div>

            <div class="col-lg-2" id="alert-success-staging-close" style="display: none;">
              <span class="closebtn mb-2" onclick="document.getElementById('alert-success').style.display='none';">&times;</span> 
            </div>

        </div>

        <div id="alert-error" class="alert-error col-lg-12" style="display: none;">

            <div class="col-lg-10" id="alert-error-staging">
            </div>

            <div class="col-lg-2" id="alert-error-staging-close" style="display: none;">
              <span class="closebtn" onclick="document.getElementById('alert-error').style.display='none';">&times;</span> 
            </div>

        </div>
        <div class="box-header with-border">

            <h3 class="box-title">{{ $post->id ? tr('edit_post') : tr('create_post') }}</h3>
            <h6 class="box-subtitle"></h6>

            <div class="box-tools pull-right">
                <a href="{{route('admin.posts.index') }}" class="btn btn-primary"><i
                        class="ft-eye icon-left"></i>{{ tr('view_posts') }}</a>
            </div>

        </div>

        <div class="card-content collapse show">

            <div class="card-body">

                <div class="card-text">

                </div>

                <form class="form-horizontal"
                    action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.posts.save') }}"
                    method="POST" enctype="multipart/form-data" role="form">

                    @csrf

                    <div class="form-body">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="page">
                                        {{tr('user_name')}}
                                        <span class="required" aria-required="true"> <span
                                                class="admin-required">*</span> </span>
                                    </label>

                                    <select class="form-control select2" name="user_id" required>

                                        <option value="">{{tr('select_user_name')}}</option>
                                        @foreach($users as $user)
                                        <option value="{{$user->id}}" @if($user->id == $post->user_id) selected="true"
                                            @endif>
                                            {{$user->name}}
                                        </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="page">
                                        {{tr('select_post_type')}}
                                    </label><br>

                                    <input type="radio" class="with-gap" id="{{POSTS_IMAGE}}"
                                        onclick="select_image_type();" name="file_type" value="{{POSTS_IMAGE}}"
                                        {{ $post->id && !empty($post_files) ? ($post_files && $post_files[0]->file_type  == POSTS_IMAGE ? "checked" : "") : ""}}>
                                    <label for="{{POSTS_IMAGE}}">{{tr('image')}}</label>
                                    <input type="radio" class="with-gap" id="{{POSTS_VIDEO}}"
                                        onclick="select_image_type();" name="file_type" value="{{POSTS_VIDEO}}"
                                        {{ $post->id && !empty($post_files) ? (($post_files && $post_files[0]->file_type  == POSTS_VIDEO) ? "checked" : "") : ""}}>
                                    <label for="{{POSTS_VIDEO}}">{{tr('video')}}</label>
                                    <input type="radio" class="with-gap" id="{{POSTS_AUDIO}}"
                                        onclick="select_image_type();" name="file_type" value="{{POSTS_AUDIO}}"
                                        {{ $post->id && !empty($post_files) ? (($post_files && $post_files[0]->file_type  == POSTS_AUDIO) ? "checked" : "") : ""}}>
                                    <label for="{{POSTS_AUDIO}}">{{tr('audio')}}</label>

                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 preview_file" style="display: none;">

                                <div class="form-group">
                                    <label for="page">{{tr('preview_file')}}</label>
                                    <p class="text-muted">{{tr('preview_file_notes')}}</p>

                                    <input type="file" class="form-control" name="preview_file" accept="image/*" />
                                </div>

                            </div>

                            <div class="col-md-6 upload_file" style="display: none;">

                                <div class="form-group">
                                    <label for="page">{{tr('upload_file')}}</label>
                                    <p class="text-muted">{{tr('post_file_notes')}}</p>

                                    <input multiple="multiple" type="file" id="upload" class="form-control"
                                        name="post_files[]" accept="image/*" />
                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 video_preview_file" style="display: none;">

                                <div class="form-group">
                                    <label for="page">{{tr('preview_video_file')}}</label>
                                    <p class="text-muted">{{tr('video_preview_file_notes')}}</p>

                                    <input type="file" class="form-control" name="video_preview_file"
                                        accept="video/*" />
                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="page">
                                        {{tr('category')}}
                                        <span class="required" aria-required="true"> <span
                                                class="admin-required">*</span> </span>
                                    </label>

                                    <select multiple="multiple" class="form-control select2" name="category_ids[]"
                                        required>

                                        @foreach($categories as $category)
                                        <option value="{{$category->id}}" @if(in_array($category->
                                            id,$post_category_details)) selected="true" @endif>
                                            {{$category->name}}
                                        </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 post_amount" style="display: none;">
                                <div class="form-group">
                                    <label for="user_name">{{ tr('token') }}</label>
                                    <input type="number" step=".01" min="1" id="amount" name="amount" class="form-control"
                                        placeholder="{{ tr('token') }}"
                                        value="{{(Setting::get('is_only_wallet_payment') ? $post->token : $post->amount) ?: old('amount') }}">
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <input type="hidden" name="post_id" id="post_id" value="{{ $post->id}}">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_name">{{ tr('content') }}</label>
                                    <textarea rows="5" name="content"
                                        class="form-control">{{ $post->content_formatted ?: old('content') }}</textarea>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="form-actions">

                        <div class="pull-right">

                            <button type="reset" class="btn btn-warning mr-1">
                                <i class="ft-x"></i> {{ tr('reset') }}
                            </button>

                            <button type="submit" class="btn btn-primary"
                                @if(Setting::get('is_demo_control_enabled')==YES) disabled
                                @endif></i>{{ tr('submit') }}</button>

                        </div>

                        <div class="clearfix"></div>

                    </div>

                </form>

            </div>

        </div>

        @if(!empty($post_files))

        <div class="user-view-padding">
            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12">
                    <hr>
                    <hr>

                    <div class="px-2 resp-marg-top-xs">

                        <div class="card-title">
                            <h4>{{tr('post_files')}}</h4>
                        </div><br>

                        <div class="row">

                            @foreach($post_files as $post_file)

                            <div class="col-xl-3 col-lg-4 col-md-12 margin-bottom-md" id="show-hide-{{$post_file->id}}">

                                @if($post_file->file_type == FILE_TYPE_IMAGE || $post_file->file_type ==
                                FILE_TYPE_VIDEO)

                                <div class="box box-body box-outline-dark rounded post-files-show show_hide_{{$post_file->id}}"
                                    style="background-image: url({{$post_file->file_type == FILE_TYPE_IMAGE ? $post_file->file : $post_file->preview_file}})">
                                    <div class="flexbox align-items-center audio-dropdown">
                                        <!-- <label class="toggler toggler-yellow">
                                        </label> -->

                                        <div class="dropdown mr-2">
                                            <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false"><i
                                                    class="ion-android-more-vertical"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{$post_file->file}}" target="_blank"><i
                                                        class="fa fa-fw fa-user"></i> {{tr('view')}}</a>
                                                <a class="dropdown-item"
                                                    onclick="post_edit_delete({{$post_file->id}},{{$post_file->post_id}})" href="#"><i
                                                        class="fa fa-fw fa-remove"></i> {{tr('delete')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @else

                                <div class="audio-post-sec">
                                    <div class="audio-post-box">
                                        <div class="audio-post-card">
                                            <div class="audio-post-bg-img-sec" style="background-image: url({{Setting::get('audio_call_placeholder')}})">
                                            </div>
                                            <div class="flexbox align-items-center audio-dropdown">
                                                <div class="dropdown">
                                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false"><i
                                                            class="ion-android-more-vertical"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="{{$post_file->file}}" target="_blank"><i
                                                                class="fa fa-fw fa-user"></i>{{tr('view')}}</a>
                                                        <a class="dropdown-item"
                                                            onclick="post_edit_delete({{$post_file->id}},{{$post_file->post_id}})" href="#"><i
                                                                class="fa fa-fw fa-remove"></i>{{tr('delete')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="audio-post-file">
                                                <audio controls controlsList="nodownload">
                                                    <source src="{{$post_file->file}}" type="audio/mpeg">
                                                </audio>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endif

                            </div>

                            @endforeach
                            

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @endif

    </div>

</section>