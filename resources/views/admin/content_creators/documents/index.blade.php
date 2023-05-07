@extends('layouts.admin') 

@section('title', tr('content_creators')) 

@section('content-header', tr('content_creators')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{ route('admin.content_creators.index') }}">{{ tr('content_creators') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('content_creator_documents') }}</a></li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('content_creator_documents') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data table-responsive">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('content_creator') }}</th>
                                    <th>{{ tr('document') }}</th>
                                    <th>{{ tr('is_email_verified') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('uploaded_by') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($stardom_documents as $i => $user_document)
                                <tr>
                                    <td>{{ $i+1 }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $user_document->user_id] )  }}">
                                        {{ $user_document->user->name  ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.documents.view' , ['document_id' => $user_document->document_id] )  }}">
                                            {{ $user_document->document->name ?? "-" }}</a>
                                    </td>

                                     <td>
                                        @if($user_document->is_email_verified == STARDOM_DOCUMENT_VERIFIED)

                                            <span class="btn btn-success btn-sm">{{ tr('verified') }}</span> 
                                        @else

                                            <span class="btn btn-danger btn-sm">{{ tr('unverified') }}</span> 
                                        @endif
                                    </td>

                                    <td>
                                        @if($user_document->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 

                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                        @endif
                                        
                                    </td>

                                    <td>
                                        <span class="badge badge-secondary">{{ $user_document->uploaded_by ?: "-" }}</span>
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{$user_document->document_file}}" target="_black"> {{ tr('document_file') }}</a> 

                                                <a class="dropdown-item" href="{{$user_document->document_file_front}}" target="_black"> {{ tr('document_file_front') }}</a> 

                                                <a class="dropdown-item" href="{{$user_document->document_file_back}}" target="_blank"> {{ tr('document_file_back') }}</a> 

                                                <div class="dropdown-divider"></div>
                                                @if($user_document->is_email_verified == STARDOM_DOCUMENT_NOT_VERIFIED)

                                                    <a class="dropdown-item" href="{{ route('admin.stardoms.documents.verify' , ['stardom_document_id' => $user_document->id]) }}">
                                                        {{ tr('verify') }}
                                                    </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.stardoms.documents.verify' , ['stardom_document_id' => $user_document->id]) }}">
                                                    {{ tr('unverify') }}
                                                </a>@endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $stardom_documents->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection