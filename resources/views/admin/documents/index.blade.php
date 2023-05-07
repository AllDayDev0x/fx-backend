@extends('layouts.admin') 

@section('title', tr('documents')) 

@section('content-header', tr('documents')) 

@section('breadcrumb')
    
<li class="breadcrumb-item active">
    <a href="{{route('admin.documents.index')}}">{{ tr('documents') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_documents') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_documents') }}</h4>

                    <div class="heading-elements">
                        <a href="{{ route('admin.documents.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_document') }}</a>
                    </div>
                    
                </div>
                 <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('document_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('document_name') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>&nbsp;&nbsp;{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($documents as $i => $document)
                                <tr>
                                    <td>{{ $i + $documents->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.documents.view' , ['document_id' => $document->id] )  }}">
                                        {{ $document->name  ?: tr('n_a')}}
                                        </a>
                                    </td>

                                    <td>
                                        @if($document->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>
                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                        @endif
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.documents.view', ['document_id' => $document->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.documents.edit', ['document_id' => $document->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('document_delete_confirmation' , $document->name) }}&quot;);" href="{{ route('admin.documents.delete', ['document_id' => $document->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($document->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.documents.status' , ['document_id' => $document->id] )  }}" onclick="return confirm(&quot;{{ $document->name }} - {{ tr('document_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.documents.status' , ['document_id' => $document->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $documents->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection