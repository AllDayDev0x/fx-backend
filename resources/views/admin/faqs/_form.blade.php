<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{ $faq->id ? tr('edit_faq') : tr('add_faq') }}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.faqs.index') }}" class="btn btn-primary"><i class="ft-file"></i>{{ tr('view_faqs') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                         <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.faqs.save') }}" method="POST" enctype="multipart/form-data" role="form">

                            @csrf

                            <div class="form-row">

                                <input type="hidden" name="faq_id" id="faq_id" value="{{ $faq->id}}">

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-control-label" for="question">{{tr('question')}}*</label>
                                        <input type="text" id="question" name="question" class="form-control" placeholder="{{tr('question_placeholder')}}" value="{{ $faq->question ?: old('question') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-control-label" for="answer">{{tr('answer')}}*</label>
                                        
                                         <textarea id="summernote" rows="5" class="form-control" name="answer" placeholder="{{ tr('answer') }}">{{old('answer') ?: $faq->answer}}</textarea>
                                       
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
