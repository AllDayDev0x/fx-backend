@extends('layouts.admin') 

@section('content-header', tr('users')) 

@section('breadcrumb')

@if($user->is_content_creator)


    
<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('content_creators') }}</a>
</li>

@else

<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

@endif

<li class="breadcrumb-item">{{tr('following')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">
                        {{ tr('followers_fans') }} - 
                        <a href="{{route('admin.users.view',['user_id' => $user->id])}}">{{$user->name ?? "-"}}</a> 
                    </h4>
                    
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                         @include('admin.users._followers_search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <!-- <th>{{ tr('username') }}</th> -->
                                    <th>{{ tr('follower') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('updated_at') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($user_followers as $i => $follwer)

                                <tr>
                                    <td>{{ $i+$user_followers->firstItem() }}</td>

                                    <!-- <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $follwer->user_id] )  }}">
                                        {{ $follwer->user->name ?? "-" }}
                                        </a>
                                    </td> -->
                                    
                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $follwer->follower_id] )  }}">
                                        {{ $follwer->followerDetails->name ?? "-"}}
                                        </a>
                                    </td>

                                    <td>
                                        @if($follwer->status == APPROVED)

                                         <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                        @endif
                                    </td>

                                    <td>{{common_date($follwer->updated_at , Auth::guard('admin')->user()->timezone)}}</td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $user_followers->appends(request()->input())->links('pagination::bootstrap-4') }}
                        </div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection