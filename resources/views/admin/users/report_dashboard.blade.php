@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')

    

    <li class="breadcrumb-item active"><a href="{{route('admin.users.index')}}">{{tr('users')}}</a>
    </li>

    <li class="breadcrumb-item">{{tr('report_dashboard')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="col-12">

        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('report_dashboard') }}</h4>
                
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch">
                                    <div class="p-2 text-center bg-success bg-darken-2">
                                        <i class="icon-trophy font-large-2 white"></i>
                                    </div>
                                    <div class="p-2 bg-gradient-x-success white media-body">
                                        <h5>{{tr('subscription_payments')}}</h5>
                                        <h5 class="text-bold-400 mb-0">{{formatted_amount($data->subscription_payments)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch">
                                    <div class="p-2 text-center bg-warning bg-darken-2">
                                        <i class="icon-diamond font-large-2 white"></i>
                                    </div>
                                    <div class="p-2 bg-gradient-x-warning white media-body">
                                        <h5>{{tr('total_payments')}}</h5>
                                        <h5 class="text-bold-400 mb-0">{{formatted_amount($data->total_payments)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch">
                                    <div class="p-2 text-center bg-info bg-darken-2">
                                        <i class="icon-like font-large-2 white"></i>
                                    </div>
                                    <div class="p-2 bg-gradient-x-info white media-body">
                                        <h5>{{tr('post_payment_earned')}}</h5>
                                        <h5 class="text-bold-400 mb-0">{{formatted_amount($data->post_payments ?? 0.00)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch">
                                    <div class="p-2 text-center bg-danger bg-darken-2">
                                        <i class="icon-heart font-large-2 white"></i>
                                    </div>
                                    <div class="p-2 bg-gradient-x-danger white media-body">
                                        <h5>{{tr('tips_earnings')}}</h5>
                                        <h5 class="text-bold-400 mb-0">{{formatted_amount($data->user_tips ?? 0.00)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <hr>

                <div class="card-content">
                        
                        <div class="user-view-padding">
                            <div class="row"> 

                                <div class=" col-xl-8 col-lg-8 col-md-12">
                                    <div class="table-responsive">

                                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                            <tr >
                                                <th>{{tr('username')}}</th>
                                                <td>
                                                    <a href="{{route('admin.users.view',['user_id'=>$user->id])}}">
                                                        {{$user->name ?: tr('n_a')}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('user_wallet_balance')}}</th>
                                                <td>
                                                    {{$user->userWallets->remaining_formatted ?? formatted_amount(0.00)}}
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('tipped_amount')}}</th>
                                                <td>
                                                    <a href="{{route('admin.user_tips.index',['user_id'=>$user->id])}}">
                                                    {{$user->tipped_amount ?? 0.00}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('post_amount')}}</th>
                                                <td>
                                                    <a href="{{route('admin.post.payments',['user_id'=>$user->id])}}">
                                                    {{$user->total_post_payment ?? 0.00}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('total_posts')}}</th>
                                                <td>
                                                    <a href="#">
                                                    {{$data->total_posts ?? 0}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('total_likes')}}</th>
                                                <td>
                                                    <a href="#">
                                                    {{$data->liked_post ?? 0}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('total_followers')}}</th>
                                                <td>
                                                    <a href="#">
                                                    {{$user->total_followers ?? 0}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('total_following')}}</th>
                                                <td>
                                                    <a href="#">
                                                    {{$user->total_followings ?? 0}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('total_fav')}}</th>
                                                <td>
                                                    <a href="#">
                                                    {{$user->total_fav_users ?? 0}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('email_notification')}}</th>
                                                <td>
                                                    @if($user->is_email_notification == YES)

                                                    <span class="badge badge-success">{{tr('on')}}</span>

                                                    @else
                                                    <span class="badge badge-danger">{{tr('off')}}</span>

                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('created_at')}} </th>
                                                <td>{{common_date($user->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('updated_at')}} </th>
                                                <td>{{common_date($user->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>


                                        </table>

                                    </div>

                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-12">

                                    <div class="px-2 resp-marg-top-xs">

                                        <div class="card-title">{{tr('action')}}</div>

                                        <div class="row">

                                            <div class="col-xl-6 col-lg-6 col-md-12" role="group">

                                                <button class="btn btn-outline-secondary dropdown-toggle dropdown-menu-right ml-3" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('weekly_report') }}</button>

                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        <a href="{{ route('admin.users.weekly_report',['downloadexcel'=>'excel','status'=>Request::get('status'),'user_id'=> $data->user_id]) }}" class="dropdown-item">&nbsp;Export to Excel</a>

                                                        <a class="dropdown-item" href="{{ route('admin.users.send_week_report', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('send_to_mail') }}</a>

                                                    </div>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12" role="group">

                                                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right ml-3" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('monthly_report') }}</button>

                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        <a href="{{ route('admin.users.monthly_report',['downloadexcel'=>'excel','status'=>Request::get('status'),'user_id'=> $data->user_id]) }}" class="dropdown-item">&nbsp;Export to Excel</a>

                                                        <a class="dropdown-item" href="{{ route('admin.users.send_monthly_report', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('send_to_mail') }}</a>

                                                    </div>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12" role="group"><br>

                                                <a class="btn btn-outline-info ml-3" href="{{ route('admin.users.view', ['user_id' => $user->id] ) }}" data-toggle="modal" data-target="#report_{{$user->id}}">&nbsp;{{tr('custom_report')}}</a>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                    </div>

                @include('admin.users._custom_report_form')

        </div>

    </div>

</div>

</section>
  
    
@endsection

