<section class="content">

    <div class="row">

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-header border-bottom border-gray">

                        <h4 class="card-title">{{ tr('add_hashtag') }}</h4>
                        <div class="heading-elements">
                            <a href="{{ route('admin.hashtags.index') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('view_hashtags') }}</a>
                        </div>
                    </div>
                <div class="card-body">
                    @if(Setting::get('is_demo_control_enabled') == NO )

                    <form class="forms-sample" action="{{ route('admin.hashtags.save') }}" method="POST" enctype="multipart/form-data" role="form">

                        @else

                        <form class="forms-sample" role="form">

                            @endif

                            @csrf

                            <div class="card-body">

                                @if($hashtag->id)

                                <input type="hidden" name="hashtag_id" value="{{$hashtag->id}}">

                                @endif

                                <div class="form-body">

                                    <div class="row">

                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <label for="title">{{tr('hashtag_title')}}<span class="admin-required">*</span> </label>
                                                <input type="text" id="title" name="title" class="form-control" placeholder="Enter {{tr('title')}}" required value="{{old('name')?: $hashtag->title}}" onkeydown="return alphaOnly(event);">
                                            </div>
                                        </div>




                                    </div>

                                    <div class="row">

                                        <div class="col-md-12">

                                            <div class="form-group">

                                                <label for="description">{{tr('description')}}<span class="admin-required">*</span></label>

                                                <textarea id="summernote" rows="10" class="form-control" name="description" placeholder="{{ tr('description') }}">{{old('description') ?: $hashtag->description}}</textarea>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="form-actions">

                                <div class="pull-right">

                                    <button type="reset" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i> {{ tr('reset') }}
                                    </button>

                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled')==YES) disabled @endif><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>

                                </div>

                                <div class="clearfix"></div>

                            </div>

                        </form>

                </div>
            </div>
        </div>

    </div>

</section>