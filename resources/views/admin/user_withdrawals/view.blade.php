@extends('layouts.admin')

@section('title', tr('view_user_withdrawals'))

@section('content-header', tr('user_withdrawals'))

@section('breadcrumb')

    <li class="breadcrumb-item">
        <a href="{{route('admin.user_withdrawals')}}">{{tr('user_withdrawals')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('view_user_withdrawals')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">
        <div class="col-md-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_user_withdrawals') }} - <!-- <a href="{{route('admin.users.view',['user_id' => $user_withdrawal->user_id])}}"> -->{{$user_withdrawal->unique_id ?? tr('n_a')}}<!-- </a> -->	</h4>   
                    
                </div>

                <div class="card-body box box-outline-info">

                    <div class="row">

                        <div class="col-lg-6">

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                
                                <tbody>

                                    <tr>
                                        <td>{{ tr('withdraw_request_id') }}</td>
                                        <td>{{$user_withdrawal->unique_id}}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ tr('username') }}</td>
                                        <td><a href="{{route('admin.users.view',['user_id'=>$user_withdrawal->user_id])}}">{{$user_withdrawal->user->name ?? tr('n_a')}}</a></td>
                                    </tr>

                                    <!-- <tr>
                                        <td>{{ tr('payment_id') }}</td>
                                        <td>{{$user_withdrawal->payment_id}}</td>
                                    </tr> -->

                                    <tr>
                                        <td>{{ tr('requested_token') }}</td>
                                        <td>{{$user_withdrawal->requested_amount_formatted}}</td>
                                    </tr>

                                    <!-- <tr>
                                        <td>{{ tr('requested_amount') }}</td>
                                        <td>{{$user_withdrawal->requested_amount_formatted}}</td>
                                    </tr> -->

                                    <tr>
                                        <td>{{ tr('paid_token') }}</td>
                                        <td>{{$user_withdrawal->paid_amount_formatted}}</td>
                                    </tr>

                                    @if($user_withdrawal->status == WITHDRAW_PAID)

                                    <tr>
                                        <td>{{ tr('paid_date') }}</td>
                                        <td>{{common_date($user_withdrawal->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                    </tr>

                                    @endif
                                    
                                    <tr>
                                        <td>{{tr('email')}}</td>
                                        <td>{{$user_withdrawal->user->email ?? tr('n_a') }}</td>
                                    </tr>

                                    <tr>
                                        <td>{{tr('payment_mode')}}</td>
                                        <td><span class="badge badge-secondary">{{$user_withdrawal->payment_mode}}</span></td>
                                    </tr>

                                    <tr>
                                        <td>{{ tr('status') }}</td>
                                        <td>
                                            @if($user_withdrawal->status == WITHDRAW_PAID)

                                                <span class="badge bg-success">{{tr('paid')}}</span>

                                            @elseif($user_withdrawal->status == WITHDRAW_INITIATED)

                                                <span class="badge bg-danger">{{tr('initiated')}}</span>

                                            @elseif($user_withdrawal->status == WITHDRAW_ONHOLD)

                                                <span class="badge bg-danger">{{tr('hold')}}</span>

                                            @elseif($user_withdrawal->status == WITHDRAW_DECLINED)

                                                <span class="badge bg-danger">{{tr('rejected')}}</span>

                                            @else
                                                <span class="badge bg-danger">{{tr('canceled')}}</span>

                                            @endif

                                        </td>
                                    </tr>

                                    @if($user_withdrawal->status == WITHDRAW_INITIATED)

                                    <tr>
                                        <td>{{tr('action')}}</td>
                                        <td>
                                            <div class="btn-group" role="group">

                                                <button class="badge bg-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                    @if(in_array($user_withdrawal->status,[ WITHDRAW_INITIATED,WITHDRAW_ONHOLD]))

                                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#paynowModal">
                                                        
                                                        <span class="nav-text">{{tr('paynow')}}</span>

                                                    </a>

                                                    <div class="dropdown-divider"></div>

                                                    <a href="{{route('admin.user_withdrawals.reject',['user_withdrawal_id'=>$user_withdrawal->id])}}" class="dropdown-item" onclick="return confirm(&quot;{{tr('user_withdrawal_reject_confirmation')}}&quot;);">{{tr('reject')}}</a>

                                                    @endif

                                                </div>

                                            </div>
                                        </td>
                                    </tr>

                                    @endif

                                </tbody>

                            </table>
                        </div>

                        <div class="col-lg-6">

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                        
                                <tbody>
                                    <tr>
                                        <td>{{tr('account_holder_name')}}</td>
                                        <td>{{$billing_account->first_name.' '.$billing_account->last_name ?? tr('n_a')}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{tr('account_no')}}</td>
                                        <td>{{$billing_account->account_number ?? tr('n_a')}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ tr('swift_code') }}</td>
                                        <td>{{$billing_account->swift_code ?? tr('n_a')}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{tr('route_number')}}</td>
                                        <td>{{$billing_account->route_number ?? tr('n_a')}}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>{{tr('bank_type')}}</td>
                                        <td>{{ucfirst($billing_account->bank_type) ?? tr('n_a')}}</td>
                                    </tr>
                                    <!-- <tr>
                                        <td>{{ tr('amount') }}</td>
                                        <td>{{$user_withdrawal->requested_amount_formatted}}</td>
                                    </tr> -->

                                    <tr>
                                        <td>{{ tr('requested_at') }}</td>
                                        <td>{{common_date($user_withdrawal->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                    </tr>

                                    @if($user_withdrawal->status == WITHDRAW_PAID)

                                    <tr>
                                        <td>{{ tr('paid_date') }}</td>
                                        <td>{{common_date($user_withdrawal->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                    </tr>

                                    @endif

                                </tbody>

                            </table>
                            
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div id="paynowModal" class="modal fade" role="dialog">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h4 class="modal-title pull-left">
                        <a href="{{route('admin.users.view' , ['user_id' => $user_withdrawal->user_id])}}"> {{ $user_withdrawal->user->name ?? tr('user_details_not_avail')}}
                        </a>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <div class="col-sm popup-label">
                            <b class="label-font">{{tr('account_holder_name')}}</b>
                            <p>{{$user_withdrawal->billingAccount->account_holder_name ?? tr('not_available') }}</p>
                        </div>

                        <div class="col-sm popup-label">
                            <b class="label-font">{{tr('account_number')}}</b>
                            <p>{{$user_withdrawal->billingAccount->account_number ?? tr('not_available') }}</p>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-sm popup-label">
                            <b class="label-font">{{tr('bank_name')}}</b>
                            <p>{{$user_withdrawal->billingAccount->bank_name ?? tr('not_available') }}</p>
                        </div>

                        <div class="col-sm popup-label">
                            <b class="label-font">{{tr('ifsc_code')}}</b>
                            <p>{{$user_withdrawal->billingAccount->ifsc_code ?? tr('not_available') }}</p>
                        </div>


                    </div>

                    <div class="row">

                        <div class="col-sm popup-label">
                            <b class="label-font">{{tr('swift_code')}}</b>
                            <p>{{$user_withdrawal->billingAccount->swift_code ?? tr('not_available') }}</p>
                        </div>

                        <div class="col-sm popup-label">
                            <b class="label-font">{{tr('created_at')}}</b>
                            <p>{{ common_date($user_withdrawal->created_at,Auth::guard('admin')->user()->timezone,'d M Y') }}</p>
                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-sm popup-label popup-left">
                        <b class="label-font">{{tr('requested_amount')}}</b>
                        <p>{{formatted_amount($user_withdrawal->requested_amount ?? '0.00')}}</p>
                    </div>

                    <div class="col-sm popup-label"></div>

                </div>

                <div class="modal-footer"><br>

                    <form class="forms-sample" action="{{ route('admin.user_withdrawals.paynow', ['user_withdrawal_id' => $user_withdrawal->id]) }}" method="GET" role="form">
                    @csrf

                        <input type="hidden" name="user_withdrawal_id" id="user_withdrawal_id" value="{{$user_withdrawal->id}}">

                        <div class="form-actions">

                                <div class="pull-right">

                                    <button type="button" class="btn btn-warning mr-1" data-dismiss="modal"><i class="ft-x"></i>{{tr('close')}}</button>

                                    <button type="submit" class="btn btn-primary" onclick="return confirm(&quot;{{tr('user_withdrawal_paynow_confirmation')}}&quot;);"><i class="fa fa-check-square-o"></i> {{ tr('paynow') }}</button>

                                </div>

                                <div class="clearfix"></div>

                            </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

</div>
  
    
@endsection

