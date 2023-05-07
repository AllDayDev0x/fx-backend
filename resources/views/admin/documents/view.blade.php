@extends('layouts.admin')

@section('title', tr('view_documents'))

@section('content-header', tr('view_documents'))

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.documents.index')}}">{{tr('documents')}}</a>
</li>

<li class="breadcrumb-item active">{{tr('view_documents')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="card">

        <div class="card-header">

            <h4 id="basic-forms" class="card-title">{{$document->name ?: tr('n_a')}}</h4>

        </div>

        <div class="card-content collapse show" aria-expanded="true">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">
                        <div class="card-title text-center"><h4><b>{{tr('document_image')}}</b></h4></div><br>

                        <img src="{{$document->picture ?: asset('placeholder.png')}}" class="document-image">
                    </div>

                    <div class="col-md-2">

                        <div class="card-title text-center"><h4><b>{{tr('action')}}</b></h4></div><br>

                         @if(Setting::get('admin_delete_control') == YES )

                            <a href="javascript:;" class="btn btn-warning mb-2" title="{{tr('edit')}}"><b>{{tr('edit')}}</b></a>

                            <a onclick="return confirm(&quot;{{ tr('document_delete_confirmation', $document->title ) }}&quot;);" href="javascript:;" class="btn btn-danger" title="{{tr('delete')}}"><b>{{tr('delete')}}</b>
                                </a>

                        @else
                            <a href="{{ route('admin.documents.edit' , ['document_id' => $document->id] ) }}" class="btn btn-warning btn-min-width ml-2 mb-1" title="{{tr('edit')}}"><b>{{tr('edit')}}</b></a>  
                                                        
                            <a onclick="return confirm(&quot;{{ tr('document_delete_confirmation', $document->name ) }}&quot;);" href="{{ route('admin.documents.delete', ['document_id' => $document->id] ) }}" class="btn btn-danger btn-min-width mb-1 ml-2" title="{{tr('delete')}}"><b>{{tr('delete')}}</i></b>
                                </a>
                        @endif

                        @if($document->status == APPROVED)

                            <a class="btn btn-info btn-min-width mb-1 ml-2" title="{{tr('decline')}}" href="{{ route('admin.documents.status', ['document_id' => $document->id]) }}" onclick="return confirm(&quot;{{$document->name}} - {{tr('document_decline_confirmation')}}&quot;);" >
                                <b>{{tr('decline')}}</b>
                            </a>

                        @else
                            
                            <a class="btn btn-success btn-min-width mr-1 mb-1" title="{{tr('approve')}}" href="{{ route('admin.documents.status', ['document_id' => $document->id]) }}">
                                <b>{{tr('approve')}}</b> 
                            </a>
                               
                        @endif
                           
                    </div>

                    <div class="col-lg-7">

                        <div class="card-title text-center"><h4><b>{{tr('document')}}</b></h4></div><br>

                        <p><strong>{{tr('document_name')}}</strong>

                            <span class="pull-right">{{$document->name ?: tr('n_a')}}
                            </span>
                            
                        </p>
                        <hr>

                       <p><strong>{{tr('status')}}</strong>

                            @if($document->status == APPROVED)
                                <span class="badge bg-success pull-right">{{tr('approved')}}</span>
                            @else
                                <span class="badge bg-danger pull-right">{{tr('declined')}}</span>
                            @endif

                        </p>
                        <hr>

                        <p><strong>{{tr('created_at')}} </strong>
                            <span class="pull-right">{{common_date($document->created_at , Auth::guard('admin')->user()->timezone)}}</span>
                        </p>
                        <hr>

                        <p><strong>{{tr('updated_at')}} </strong>
                            <span class="pull-right">{{common_date($document->updated_at , Auth::guard('admin')->user()->timezone)}}
                            </span>
                        </p>
                        <hr>

                        <p><strong>{{tr('description')}}</strong>
                            <span class="pull-right">{{$document->description ?: tr('n_a')}}</span>
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
  
@endsection

