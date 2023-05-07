@extends('layouts.admin') 

@section('title', tr('view_report_reason'))

@section('content-header',tr('report_reasons'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.report_reasons.index')}}">{{tr('report_reasons')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_report_reason')}}</span>
    </li>
           
@endsection  

@section('content')

<section class="content">
    
    <div class="row match-height">
    
        <div class="col-lg-12 col-md-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{tr('view_report_reason')}}</h4>
                    
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">


                        <div class="card-group">

                            <div class="card card-margin-btm-zero">

                                <div class="card-body">

                                    <h4 class="card-title">{{ tr('description') }}</h4>
                            
                                    <p class="card-text ml-4"><?= $report_reason->description ?? tr('n_a') ?></p>
                                    
                                </div>

                            </div>
                          
                            <div class="card card-margin-btm-zero">

                                <div class="card-body">

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                        <tbody>

                                            <tr>
                                                <td>{{ tr('title') }}</td>
                                                <td>{{$report_reason->title ?: tr('n_a')}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('status') }}</td>
                                                <td>
                                                    @if($report_reason->status == APPROVED)

                                                    <span class="badge bg-success">{{tr('approved')}}</span>

                                                    @else
                                                    <span class="badge bg-danger">{{tr('pending')}}</span>

                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('created_at') }}</td>
                                                <td>{{common_date($report_reason->created_at,Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('updated_at') }}</td>
                                                <td>{{common_date($report_reason->updated_at,Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                        </tbody>

                                    </table>

                                    <hr> 

                                    <div class="custom-card">
                                        <div class="row">
                                            
                                        
                                        @if(Setting::get('is_demo_control_enabled') == NO)
                                            <div class="col-md-4 col-lg-4 resp-mrg-btm-xs">

                                                <a href="{{ route('admin.report_reasons.edit', ['report_reason_id'=> $report_reason->id] ) }}" class="btn btn-primary btn-block">{{tr('edit')}}</a>
                                                
                                            </div>                              

                                            <div class="col-md-4 col-lg-4 resp-mrg-btm-xs">
                                                <a onclick="return confirm(&quot;{{tr('report_reason_delete_confirmation' , $report_reason->title)}}&quot;);" href="{{ route('admin.report_reasons.delete', ['report_reason_id'=> $report_reason->id] ) }}" class="btn btn-danger btn-block">
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

                                        @if($report_reason->status == APPROVED)

                                            <div class="col-md-4 col-lg-4">
                                                
                                                <a class="btn btn-warning btn-block" href="{{ route('admin.report_reasons.status', ['report_reason_id'=> $report_reason->id] ) }}" onclick="return confirm(&quot;{{ $report_reason->title }}-{{tr('report_reason_decline_confirmation' , $report_reason->title)}}&quot;);">

                                                    {{tr('decline')}}
                                                </a>
                                            </div>

                                        @else

                                            <div class="col-md-4 col-lg-4">
                                                 <a class="btn btn-success btn-block" href="{{ route('admin.report_reasons.status', ['report_reason_id'=> $report_reason->id] ) }}">
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
