<section class="content">
           
            <div class="box">    
                <div class="box-header with-border">

                    <h3 class="box-title">{{$document->id ? tr('edit_document') : tr('add_document') }}</h3>
                    <h6 class="box-subtitle"></h6>

                    <div class="box-tools pull-right">
                        <a href="{{route('admin.documents.index') }}" class="btn btn-primary"><i class="ft-file"></i>{{ tr('view_documents') }}</a>
                    </div>

                </div>
                 <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('add_document_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.documents.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="document_id" id="document_id" value="{{ $document->id}}">

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label for="name">{{ tr('name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $document->name ?: old('name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>

                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label>{{ tr('select_picture') }}</label>

                                            <input class="form-control"  type="file" id="picture" name="picture" accept="image/png,image/jpeg" >
                                            <p class="text-muted">{{tr('image_validate')}}</p>
                                                                            
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-12">
                                        
                                        <label for="description"><b>{{ tr('description') }}</b></label>

                                        <p class="text-muted">{{tr('document_description_note')}}</p>
                                        
                                        <textarea class="form-control" name="description" placeholder="{{ tr('description') }}">{{ $document->description ? $document->description :old('description') }}</textarea>
                                       
                                    </div>

                                </div>

                            </div><br>
                          
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

