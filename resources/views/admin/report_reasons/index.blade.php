@extends('layouts.admin') 

@section('content-header', tr('report_reasons'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.report_reasons.index' )}}">{{tr('report_reasons')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_report_reasons') }}</span>
    </li>
           
@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_report_reasons') }}</h4>

                    <div class="heading-elements">
                        <a href="{{ route('admin.report_reasons.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_report_reason') }}</a>
                    </div>
                    
                </div>

                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>{{tr('report_reasons_notes')}}</li>
                            </ul>
                        <p></p>
                    </div>
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">
                    
                    <div class="table-responsive">

                        <form method="GET" action="{{route('admin.report_reasons.index')}}">

                            <div class="row">

                                <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">
                                </div>

                                <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

                                    <div class="input-group">

                                        <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('report_reason_search_placeholder')}}"> 

                                        <span class="input-group-btn">
                                            &nbsp

                                            <button type="submit" class="btn btn-default reset-btn">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                            </button>

                                            <a href="{{route('admin.report_reasons.index')}}" class="btn btn-default reset-btn">
                                                <i class="fa fa-eraser" aria-hidden="true"></i>
                                            </a>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </form>

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('reason_title')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>&nbsp;&nbsp;{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>


                                @foreach($report_reasons as $i => $report_reason)

                                    <tr>
                                        <td>{{$i+$report_reasons->firstItem()}}</td>

                                        <td>
                                            <a href="{{route('admin.report_reasons.view' , ['report_reason_id'=> $report_reason->id] )}}"> {{$report_reason->title ?: tr('n_a')}}</a>
                                        </td>

                                        <td>
                                            @if($report_reason->status == APPROVED)

                                              <span class="badge badge-success">{{tr('approved')}}</span> 

                                            @else

                                              <span class="badge badge-warning">{{tr('pending')}}</span> 
                                            @endif
                                        </td>

                                        <td>  

                                            <div class="btn-group" role="group">

                                                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">

                                                    <a class="dropdown-item" href="{{ route('admin.report_reasons.view', ['report_reason_id' => $report_reason->id] ) }}">
                                                        {{tr('view')}}
                                                    </a>

                                                    @if(Setting::get('is_demo_control_enabled') == NO)
                                                    
                                                        <a class="dropdown-item" href="{{ route('admin.report_reasons.edit', ['report_reason_id' => $report_reason->id] ) }}">
                                                            {{tr('edit')}}
                                                        </a>

                                                        <a class="dropdown-item" 
                                                        onclick="return confirm(&quot;{{tr('report_reason_delete_confirmation' , $report_reason->title)}}&quot;);" href="{{ route('admin.report_reasons.delete', ['report_reason_id' => $report_reason->id,'page'=>request()->input('page')] ) }}" >
                                                            {{ tr('delete') }}
                                                        </a>

                                                    @else

                                                        <a class="dropdown-item text-muted" href="javascript:;">{{tr('edit')}}</a>

                                                        <a class="dropdown-item text-muted" href="javascript:;">{{ tr('delete') }}</a>

                                                    @endif                  

                                                    <div class="dropdown-divider"></div>

                                                    @if($report_reason->status == APPROVED)

                                                        <a class="dropdown-item" href="{{ route('admin.report_reasons.status', ['report_reason_id' =>  $report_reason->id] ) }}" 
                                                        onclick="return confirm(&quot;{{$report_reason->title}} - {{tr('report_reason_decline_confirmation')}}&quot;);"> 
                                                            {{tr('decline')}}
                                                        </a>

                                                    @else

                                                        <a class="dropdown-item" href="{{ route('admin.report_reasons.status', ['report_reason_id' =>  $report_reason->id] ) }}">
                                                            {{tr('approve')}}
                                                        </a>
                                                           
                                                    @endif

                                                </div>
                                                 
                                            </div>
                                        

                                        </td>
                                    
                                    </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $report_reasons->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection