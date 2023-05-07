@extends('layouts.admin') 

@section('title', tr('users')) 

@section('content-header', tr('users')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{ route('admin.users.index') }}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('documents') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <p>{{tr('user_documents_verify_notes')}}</p>

            <div class="card user-document-verify-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('verification_documents') }} - <a class="" href="{{route('admin.users.view',['user_id'=> $user->id])}}">{{ $user->name ?? tr('n_a')}}</a></h4>

                    <div class="heading-elements">

                        @if($user->is_document_verified != USER_DOCUMENT_APPROVED)

                            @if($user_documents->count() > 0)

                                <a class="btn btn-success" onclick="return confirm(&quot;{{ tr('user_document_approval_confirmation' , $user->name) }}&quot;);" href="{{route('admin.user_documents.verify', ['user_id' => $user->user_id,'status'=>USER_DOCUMENT_APPROVED])}}"><i class="icon-badge"></i> {{tr('verify')}}
                                </a>

                            @endif

                            @if($user_documents->count() <= 0)

                                <button class="btn btn-info text-capitalize" disabled>{{tr('user_documents_waiting_upload')}}</button>

                            @endif

                        @else

                            <a class="btn btn-success" href="#"><i class="icon-badge"></i> {{tr('verified')}}
                            </a>

                        @endif


                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('document_name') }}</th>
                                    <th>{{ tr('updated_on') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                        
                                @foreach($user_documents as $i => $document)

                                <tr>

                                    <td>{{ $i+1 }}</td>

                                    <td>
                                        <a href="{{ route('admin.documents.view',['document_id' => $document->document_id ]) }}">{{$document->document->name ?? "-"}} </a>
                                    </td>

                                    <td>
                                        {{common_date($document->updated_at, Auth::guard('admin')->user()->timezone)}}
                                    </td>

                                    <td>
                                        <a href='{{ $document->document_file ? $document->document_file :"#"}}' target="_blank">
                                            <span class="btn btn-warning btn-large">{{ tr('view') }}</span>
                                        </a>
                                    </td>

                                </tr>

                                @endforeach
                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $user_documents->appends(request()->input())->links('pagination::bootstrap-4') }}</div>
                        
                    </div>
                
                </div>
            </div>
        </div>
    </div>

</section>

@endsection