@extends('layouts.admin')

@section('title', tr('users'))

@if($page == 'users-documents')

    @section('content-header', tr('verification_documents'))

@else

    @section('content-header', tr('users'))

@endif

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{tr('verification_documents')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{tr('verification_documents')}}</h4>

            </div>
            <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('doument_verificatin_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
            <div class="box box-outline-purple">
                <div class="box-body">
                    <div class="table-responsive">

                        @include('admin.users.documents._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('username')}}</th>
                                    <th>{{tr('email_mobile')}}</th>
                                    <th>{{tr('no_of_documents')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($users as $i => $user)

                                <tr>
                                    <td>{{$i+$users->firstItem()}}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $user->id])}}">
                                            {{ $user->name  ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        {{$user->email}}
                                        <span>
                                            <h6>{{$user->mobile?: tr('not_available')}}</h6>
                                        </span>
                                    </td>

                                    <td>
                                        <a class="btn btn-outline-pink" href="{{route('admin.user_documents.view', ['user_id' => $user->id])}}">
                                            {{$user->userDocuments->count()}}
                                        </a>
                                    </td>

                                    <td>
                                        @if($user->status == USER_DOCUMENT_APPROVED)
                                        <span class="btn btn-success btn-sm">{{tr('approved')}}</span>
                                        @else
                                        <span class="btn btn-warning btn-sm">{{tr('declined')}}</span>

                                        @endif

                                    </td>

                                    <td>

                                        @if($user->documents_count > 0)

                                        @if($user->is_document_verified != USER_DOCUMENT_APPROVED)

                                        <a class="btn btn-success" href="{{route('admin.user_documents.verify', ['user_id' => $user->user_id,'status'=>USER_DOCUMENT_APPROVED])}}" onclick="return confirm(&quot;{{tr('user_document_verify_confirmation')}}&quot;);">
                                            {{tr('verify')}}
                                        </a>

                                        @else

                                        <a class="btn btn-success" href="{{route('admin.user_documents.verify', ['user_id' => $user->user_id,'status'=>USER_DOCUMENT_DECLINED])}}" onclick="return confirm(&quot;{{tr('user_document_verify_confirmation')}}&quot;);">
                                            {{tr('decline')}}
                                        </a>

                                        @endif

                                        <a class="btn btn-warning" href="{{route('admin.user_documents.view', ['user_id' => $user->id])}}">
                                            {{ tr('view_all_documents') }}
                                        </a>
                                        @else

                                        <a class="btn btn-success" href="#" onClick="alert(&quot;{{tr('user_documents_empty')}}&quot;)">
                                            {{tr('verify')}}
                                        </a>
                                        @endif

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $users->appends(request()->input())->links('pagination::bootstrap-4') }}</div>


                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->

@endsection