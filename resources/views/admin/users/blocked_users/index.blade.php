@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{$title ?? tr('view_users')}}</li>

@endsection

@section('content')

<section class="content">
    
    <div class="row">

        <div class="col-12">

            <div class="card blocked-user-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{$title ?? tr('view_users')}}

                        @if($user) 
                        - 
                        <a href="{{route('admin.users.view',['user_id' => $user->id])}}">{{$user->name ?? tr('n_a')}}</a>

                        @endif

                    </h4>

                    <div class="heading-elements">

                    </div>



                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">
                     
                       @include('admin.users.blocked_users._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('blocked_user') }}</th>
                                    <th>{{ tr('email') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <!-- <th>{{ tr('blocked_count') }}</th> -->
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($block_users as $i => $user)

                                <tr>

                                    <td>{{ $i+$block_users->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $user->blocked_to])}}" class="custom-a">
                                            {{$user->blockeduser->name ?? tr('n_a')}}
                                        </a>

                                    </td>

                                    <td>
                                            {{$user->blockeduser->email ?? tr('n_a')}}
                                    </td>

                                    <td>
                                        @if($user->blockeduser->status == USER_APPROVED)

                                        <span class="badge badge-success">{{ tr('approved') }}</span>

                                        @else

                                        <span class="badge badge-warning">{{ tr('declined') }}</span>

                                        @endif
                                    </td>


                                    <!-- <td>
                                         <a href="{{route('admin.block_users.view' , ['user_id' => $user->blocked_to])}}" class="custom-a">
                                          {{$user->blocked_count ?? tr('n_a')}}
                                         </a>
                                    </td> -->

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.block_users.view', ['user_id' => $user->blocked_to] ) }}">&nbsp;{{ tr('blocked_list') }}</a>

                                                @if($user->blockeduser->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.users.status' , ['user_id' => $user->blocked_to] )  }}" onclick="return confirm(&quot;{{ $user->blockeduser->name ?? '' }} - {{ tr('user_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.status' , ['user_id' => $user->blocked_to] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('user_delete_confirmation' , $user->blockeduser->name ?? '') }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user->blocked_to,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>


                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('unblock_confirmation' , $user->blockeduser->name ?? '') }}&quot;);" href="{{ route('admin.block_users.delete', ['user_id' => $user->blocked_to,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('unblock_from_all_accounts') }}</a>

                                            </div>

                                        </div>

                                    </td>

                                </tr>


                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $block_users->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection