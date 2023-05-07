@extends('layouts.admin') 

@section('content-header', tr('user_session'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.user_login_session.index' )}}">{{tr('user_session')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_user_session') }}</span>
    </li>
           
@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_user_session') }}

                    @if($user)
                    - 
                    <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a>

                    @endif

                    </h4>
                    
                </div>

                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>{{tr('user_session_notes')}}</li>
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
                                    <th>{{tr('s_no')}}</th>
                                    <!-- <th>{{tr('username')}}</th> -->
                                    <th>{{tr('device_type')}}</th>
                                    <th>{{tr('device_model')}}</th>
                                    <th>{{tr('browser_type')}}</th>
                                    <th>{{tr('ip_address')}}</th>
                                    <th>{{tr('is_current_session')}}</th>
                                    <th>{{tr('last_session')}}</th>
                                    <!-- <th>&nbsp;&nbsp;{{tr('action')}}</th> -->
                                </tr>
                            </thead>

                            <tbody>


                                @foreach($user_login_sessions as $i => $user_login_session)

                                    <tr>
                                        <td>{{$i+$user_login_sessions->firstItem()}}</td>

                                        <!-- <td>
                                            <a href="{{route('admin.users.view',['user_id'=> $user_login_session->user->id] )}}">   
                                                {{$user_login_session->user->name ?: tr('n_a')}}
                                            </a>
                                        </td> -->

                                        <td>{{$user_login_session->device_type ?: tr('n_a')}}</td>

                                        <td>{{$user_login_session->device_model ?: tr('n_a')}}</td>

                                        <td>{{$user_login_session->browser_type ?: tr('n_a')}}</td>

                                        <td>{{$user_login_session->ip_address ?: tr('n_a')}}</td>

                                        <td>
                                            @if($user_login_session->is_current_session == YES)
                                                
                                            <span class="badge badge-success">{{ tr('yes') }}</span>

                                            @else

                                            <span class="badge badge-primary">{{ tr('no') }}</span>

                                            @endif

                                        </td>

                                        <td>{{common_date($user_login_session->last_session , Auth::guard('admin')->user()->timezone)}}</td>
                                    
                                    </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $user_login_sessions->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection