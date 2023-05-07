@extends('layouts.admin') 

@section('content-header', tr('users')) 

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{tr('followings')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">
                        {{ tr('followings') }}
                        @if($user) 
                        - 
                        <a href="{{route('admin.users.view',['user_id' => $user->id])}}">{{$user->name ?? tr('n_a')}}</a>

                        @endif
                    </h4>

                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        @include('admin.users._followings_search')
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <!-- <th>{{ tr('username') }}</th> -->
                                    <th>{{ tr('following_user_name') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('updated_at') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($followings as $i => $following)

                                <tr>
                                    <td>{{ $i+$followings->firstItem() }}</td>

                                    <!-- <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $following->follower_id] )  }}">
                                        {{ $following->followerDetails->name ?? "-"}}
                                        </a>
                                    </td> -->

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $following->user_id] )  }}">
                                        {{ $following->user->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        @if($following->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>

                                    <td>{{common_date($following->updated_at , Auth::guard('admin')->user()->timezone)}}</td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $followings->appends(request()->input())->links('pagination::bootstrap-4') }}
                        </div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection