<section class="content">

    <div class="row match-height">

        <div class="col-lg-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$category->id ? tr('edit_category') : tr('add_category')}}</h4>

                    <div class="heading-elements">
                        <a href="{{route('admin.categories.index') }}" class="btn btn-primary"><i class="ft-user icon-left"></i>{{ tr('view_category') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.categories.save') }}" method="POST" enctype="multipart/form-data" role="form">

                            @csrf

                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="category_id" id="category_id" value="{{ $category->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('category_name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $category->name ?: old('name') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ tr('select_picture') }}</label>

                                            <input type="file" class="form-control" id="picture" name="picture" accept="image/png,image/jpeg" src="{{ $category->picture ? $category->picture : asset('placeholder.png') }}">
                                            <p class="text-muted">{{tr('image_validate')}}</p>
                                            
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-12">

                                    <div class="form-group">

                                        <label for="description">{{tr('description')}}</label>
                                        <p class="text-muted">{{tr('document_description_note')}}</p>
                                        <textarea id="summernote" rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}">{{old('description') ?: $category->description}}</textarea>

                                    </div>

                                </div>

                            </div>

                            <div class="form-actions">

                                <div class="pull-right">

                                    <button type="reset" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i> {{ tr('reset') }}
                                    </button>

                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled')==YES) disabled @endif>{{ tr('submit') }}</button>

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