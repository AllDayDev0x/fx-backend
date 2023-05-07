@extends('layouts.admin') 

@section('title', tr('view_static_page'))

@section('content-header',tr('static_pages'))

@section('breadcrumb')

    

    <li class="breadcrumb-item"><a href="{{route('admin.static_pages.index')}}">{{tr('static_pages')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_static_page')}}</span>
    </li>
           
@endsection  

@section('content')

<section class="content">
    
    <div class="row match-height">
    
        <div class="col-lg-12 col-md-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{tr('view_static_page')}} - {{$static_page->title}}</h4>
                    
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">


                        <div class="card-group">

                            <div class="card card-margin-btm-zero">

                                <div class="card-body">

                                    <h4 class="card-title">{{ tr('description') }}</h4>
                            
                                    <p class="card-text ml-4"><?= $static_page->description ?? tr('n_a') ?></p>
                                    
                                </div>

                            </div>
                          
                            <div class="card card-margin-btm-zero">

                                <div class="card-body">

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                        <tbody>

                                            <tr>
                                                <td>{{ tr('title') }}</td>
                                                <td>{{$static_page->title ?: tr('n_a')}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('static_page_type') }}</td>
                                                <td>{{$static_page->type ?: tr('n_a')}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('section_type') }}</td>
                                                @if($static_page->section_type != 'null')
                                                    @foreach($section_types as $key => $value)

                                                       @if($static_page->section_type == $key)
                                                        <td>{{ $value }}</td>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <td>{{ tr('n_a') }}</td>
                                                @endif
                                                
                                                
                                            </tr>

                                            <tr>
                                                <td>{{ tr('status') }}</td>
                                                <td>
                                                    @if($static_page->status == APPROVED)

                                                    <span class="badge bg-success">{{tr('approved')}}</span>

                                                    @else
                                                    <span class="badge bg-danger">{{tr('pending')}}</span>

                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('created_at') }}</td>
                                                <td>{{common_date($static_page->created_at,Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('updated_at') }}</td>
                                                <td>{{common_date($static_page->updated_at,Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                        </tbody>

                                    </table>

                                    <hr> 

                                    <div class="custom-card">
                                        <div class="row">
                                            
                                        
                                        @if(Setting::get('is_demo_control_enabled') == NO)
                                            <div class="col-md-4 col-lg-4 resp-mrg-btm-xs">

                                                <a href="{{ route('admin.static_pages.edit', ['static_page_id'=> $static_page->id] ) }}" class="btn btn-primary btn-block">{{tr('edit')}}</a>
                                                
                                            </div>                              

                                            <div class="col-md-4 col-lg-4 resp-mrg-btm-xs">
                                                <a onclick="return confirm(&quot;{{tr('static_page_delete_confirmation' , $static_page->title)}}&quot;);" href="{{ route('admin.static_pages.delete', ['static_page_id'=> $static_page->id] ) }}" class="btn btn-danger btn-block">
                                                    {{ tr('delete') }}
                                                </a>

                                            </div>                               

                                        @else
                                        
                                            <div class="col-md-4 col-lg-4 resp-mrg-btm-xs">
                                                
                                                <button class="btn btn-primary btn-block" disabled>{{ tr('edit') }}</button>

                                            </div>
                                            
                                            <div class="col-md-4 col-lg-4 resp-mrg-btm-xs">
                                                
                                                <button class="btn btn-warning btn-block" disabled>{{ tr('delete') }}</button>
                                            </div>
                                            

                                        @endif

                                        @if($static_page->status == APPROVED)

                                            <div class="col-md-4 col-lg-4">
                                                
                                                <a class="btn btn-warning btn-block" href="{{ route('admin.static_pages.status', ['static_page_id'=> $static_page->id] ) }}" onclick="return confirm(&quot;{{ $static_page->title }}-{{tr('static_page_decline_confirmation' , $static_page->title)}}&quot;);">

                                                    {{tr('decline')}}
                                                </a>
                                            </div>

                                        @else

                                            <div class="col-md-4 col-lg-4">
                                                 <a class="btn btn-success btn-block" href="{{ route('admin.static_pages.status', ['static_page_id'=> $static_page->id] ) }}">
                                                    {{tr('approve')}}
                                                </a>
                                            </div>
                                               
                                        @endif

                                        </div>

                                    </div>

                                </div>
                                <!-- Card content -->

                            </div>

                            <!-- Card -->

                        </div>

                        
                    </div>
                
                </div>

            </div>
        
        </div>
    
    </div>

</section>


@endsection
