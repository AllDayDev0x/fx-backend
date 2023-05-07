@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('blocked_users'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{$title ?? tr('blocked_list')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">
                        {{$title ?? tr('view_users')}} - {{$user->name ?? ''}}
                    </h4>

                    <div class="heading-elements">

                        <a onclick="return confirm(&quot;{{ tr('user_delete_confirmation' , $user->name ?? '') }}&quot;);" href="{{route('admin.users.delete', ['user_id' => $user->id,'page'=>request()->input('page')] )}}" class="btn btn-primary">{{ tr('delete_user') }}</a>

                    </div>

                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <!-- <th>{{ tr('username') }}</th> -->
                                    <th>{{ tr('blocked_by') }}</th>
                                    <!-- <th>{{ tr('reason') }}</th> -->
                                    <!-- <th>{{ tr('status') }}</th> -->
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($blocked_users as $i => $user)

                                <tr>

                                    <td>{{ $i+$blocked_users->firstItem() }}</td>

                                    <!-- <td>
                                        {{$user->blockeduser->name ?? tr('n_a')}}

                                    </td> -->

                                    <td>
                                        {{$user->user->name ?? tr('n_a')}}
                                    </td>

                                    <!-- <td>
                                        {{$user->reason ?:tr('not_available')}}
                                    </td> -->

                                    <!-- <td>
                                        @if($user->status == USER_APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>

                                        @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span>

                                        @endif
                                    </td> -->

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('unblock_confirmation') }}&quot;);" href="{{ route('admin.block_users.delete', ['block_user_id' => $user->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('unblock_user') }}</a>

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>


                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $blocked_users->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection