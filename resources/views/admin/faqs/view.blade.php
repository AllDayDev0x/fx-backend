@extends('layouts.admin')

@section('title', tr('faqs'))

@section('content-header', tr('faqs'))

@section('breadcrumb')
    

    <li class="breadcrumb-item"><a href="{{route('admin.faqs.index')}}">{{tr('faqs')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('view_faqs')}}</a>
    </li>

@endsection

@section('content')

<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{tr('view_faqs')}}</h4>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="card-group">

                            <div class="card mb-2">

                                <div class="card-body">

                                    <div class="custom-card">
                                    
                                        <h6 class="card-title">{{tr('question')}}</h6>
                                        
                                        <p class="card-text">{{$faq->question}}</p>

                                    </div> 
                                    <hr>

                                    <div class="custom-card">
                                    
                                        <h5 class="card-title">{{tr('answer')}}</h5>
                                        
                                        <p class="card-text"><?= $faq->answer ?></p>

                                    </div>
                                    <hr>

                                    <div class="custom-card">
                                    
                                        <h5 class="card-title">{{tr('status')}}</h5>
                                        
                                        <p class="card-text">

                                            @if($faq->status == APPROVED)

                                                <span class="badge badge-success badge-md text-uppercase">{{tr('approved')}}</span>

                                            @else 

                                                <span class="badge badge-danger badge-md text-uppercase">{{tr('pending')}}</span>

                                            @endif
                                        
                                        </p>

                                    </div>
                                    <hr>
                                                            
                                    <div class="custom-card">
                                    
                                        <h5 class="card-title">{{tr('updated_at')}}</h5>
                                        
                                        <p class="card-text">{{ common_date($faq->updated_at,Auth::guard('admin')->user()->timezone) }}</p>

                                    </div>
                                    <hr>

                                    <div class="custom-card">
                                    
                                        <h5 class="card-title">{{tr('created_at')}}</h5>
                                        
                                        <p class="card-text">{{ common_date($faq->created_at,Auth::guard('admin')->user()->timezone) }}</p>

                                    </div>
                                    <hr> 

                                    <div class="custom-card">
                                        <div class="row">
                                            
                                        
                                        @if(Setting::get('is_demo_control_enabled') == NO)
                                            <div class="col-md-4 col-lg-4">

                                                <a href="{{ route('admin.faqs.edit', ['faq_id'=> $faq->id] ) }}" class="btn btn-primary btn-block">{{tr('edit')}}</a>
                                                
                                            </div>                              

                                            <div class="col-md-4 col-lg-4">
                                                <a onclick="return confirm(&quot;{{tr('faq_delete_confirmation' , $faq->question)}}&quot;);" href="{{ route('admin.faqs.delete', ['faq_id'=> $faq->id] ) }}" class="btn btn-danger btn-block">
                                                    {{ tr('delete') }}
                                                </a>

                                            </div>                               

                                        @else
                                        
                                            <div class="col-md-4 col-lg-4">
                                                
                                                <button class="btn btn-primary btn-block" disabled>{{ tr('edit') }}</button>

                                            </div>
                                            
                                            <div class="col-md-4 col-lg-4">
                                                
                                                <button class="btn btn-warning btn-block" disabled>{{ tr('delete') }}</button>
                                            </div>
                                            

                                        @endif

                                        @if($faq->status == APPROVED)

                                            <div class="col-md-4 col-lg-4">
                                                
                                                <a class="btn btn-warning btn-block" href="{{ route('admin.faqs.status', ['faq_id'=> $faq->id] ) }}" onclick="return confirm(&quot;{{ $faq->question }}-{{tr('faq_decline_confirmation' , $faq->title)}}&quot;);">

                                                    {{tr('decline')}}
                                                </a>
                                            </div>

                                        @else

                                            <div class="col-md-4 col-lg-4">
                                                 <a class="btn btn-success btn-block" href="{{ route('admin.faqs.status', ['faq_id'=> $faq->id] ) }}">
                                                    {{tr('approve')}}
                                                </a>
                                            </div>
                                               
                                        @endif

                                        </div>

                                    </div>

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

