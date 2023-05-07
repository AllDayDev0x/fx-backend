<section class="content">
    
            <div class="box">    
                <div class="box-header with-border">

                    <h3 class="box-title">{{$report_reason->id ? tr('edit_report_reason') : tr('add_report_reason')}}</h3>
                    <h6 class="box-subtitle"></h6>

                    <div class="box-tools pull-right">
                        <a href="{{route('admin.report_reasons.index') }}" class="btn btn-primary"><i class="ft-file icon-left"></i>{{ tr('view_report_reason') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        @if(Setting::get('is_demo_control_enabled') == NO )

                        <form class="forms-sample" action="{{ route('admin.report_reasons.save') }}" method="POST" enctype="multipart/form-data" role="form">

                        @else

                            <form class="forms-sample" role="form">

                        @endif 

                            @csrf

                            <!-- <div class="card-body"> -->

                                @if($report_reason->id)

                                    <input type="hidden" name="report_reason_id" value="{{$report_reason->id}}">

                                @endif

                                <div class="form-body">

                                    <div class="row">

                                        <div class="form-group col-md-6">
                                            <div class="form-group-1">
                                                <label for="title">{{tr('report_title')}}<span class="admin-required">*</span> </label>
                                                <input type="text" id="title" name="title" class="form-control" placeholder="Enter {{tr('report_title')}}" required  value="{{old('title')?: $report_reason->title}}" onkeydown="return alphaOnly(event);">
                                            </div>
                                        </div>


                                    </div>
                                    
                                    <div class="row">

                                        <div class="col-md-12"> 

                                            <div class="form-group">

                                                <label for="description">{{tr('description')}}<span class="admin-required">*</span></label>

                                                <textarea rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}" required>{{old('description') ?: $report_reason->description}}</textarea>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            <!-- </div> -->

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

