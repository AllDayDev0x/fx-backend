@extends('layouts.admin')

@section('title', tr('view_user_wallets'))

@section('content-header', tr('user_wallets'))

@section('breadcrumb')



<li class="breadcrumb-item"><a href="{{route('admin.user_wallets.index')}}">{{tr('user_wallets')}}</a>
</li>

<li class="breadcrumb-item active">{{tr('view_user_wallets')}}</a>
</li>

@endsection

@section('content')

<section class="content">

    <div class="card">

        <div class="card-header border-bottom border-gray">

            <h4 class="card-title">{{ tr('wallet_history_payment_details') }} - <!-- <a
                    href="{{route('admin.users.view',['user_id' => $user_wallet_payment->user_id])}}"> -->{{$user_wallet_payment->payment_id ?? tr('n_a')}}<!-- </a> -->
            </h4>

        </div>

        <div class="card-body">

            <div class="card user-profile-view-sec">

                  

                    <div class="card-content">

                        <div class="user-view-padding">
                            <div class="row"> 

                                <div class=" col-xl-6 col-lg-6 col-md-12">
                                    <div class="table-responsive">

                                        <table class="table table-xl mb-0">
                                            <tr >
                                                <th>{{tr('user_name')}}</th>
                                                <td>
                                                    <a href="{{route('admin.users.view',['user_id' => $user_wallet_payment->user_id])}}">{{$user_wallet_payment->user->name?? tr('n_a')}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ tr('payment_id') }}</th>
                                                <td>{{ $user_wallet_payment->payment_id ?: tr('n_a')}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('date')}}</th>
                                                <td>
                                                   {{common_date($user_wallet_payment->paid_date, Auth::user()->timezone)}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('payment_mode')}} </th>
                                                <td>{{$user_wallet_payment->payment_mode}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('paid_amount')}} </th>
                                                <td>{{$user_wallet_payment->paid_amount_formatted}}</td>
                                            </tr>
                                             <tr>
                                                <th>{{tr('admin_amount')}} </th>
                                                <td>{{$user_wallet_payment->admin_amount_formatted}}</td>
                                            </tr>
                                             <tr>
                                                <th>{{tr('user_amount')}} </th>
                                                <td>{{$user_wallet_payment->user_amount_formatted}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('message')}} </th>
                                                <td>{{ $user_wallet_payment->message }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('status')}} </th>
                                                <td>

                                                    @if($user_wallet_payment->status == PAID)

                                                    <span class="btn btn-success btn-sm">{{ tr('paid') }}</span>
                                                    @else

                                                    <span class="btn btn-warning btn-sm">{{ tr('not_paid') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                             <tr>
                                                <th>{{tr('created_at')}} </th>
                                                <td>{{common_date($user_wallet_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr> 
                                            <tr>
                                                <th>{{tr('updated_at')}} </th>
                                                <td>{{common_date($user_wallet_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr> 

                                        </table>

                                    </div>
                                </div>
                        
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection