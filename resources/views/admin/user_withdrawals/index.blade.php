@extends('layouts.admin')

@section('title', tr('revenue_management'))

@section('content-header', tr('revenue_management'))

@section('breadcrumb')
<li class="breadcrumb-item active">
    <a href="{{route('admin.user_withdrawals')}}">{{ tr('revenue_management') }}</a>
</li>

<li class="breadcrumb-item">{{ tr('user_withdrawals') }}</a></li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-lg-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('user_withdrawals')}}  
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
                                <li>
                                    {{tr('withdrawl_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">
                    
                    <div class="table-responsive">

                        @include('admin.user_withdrawals._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('withdraw_request_id') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('requested_token') }}</th>
                                    <th>{{ tr('amount') }}</th> 
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($user_withdrawals as $i => $user_withdrawal)
                                <tr>
                                    <td>{{ $i+$user_withdrawals->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.user_withdrawals.view',['user_withdrawal_id'=>$user_withdrawal->id])}}" class="dropdown-item">
                                            {{ $user_withdrawal->unique_id ?: tr('n_a')}}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $user_withdrawal->user_id] )  }}">
                                            {{ $user_withdrawal->user->name ?? tr('n_a') }}
                                        </a>
                                    </td>
                                    
                                    <td>
                                        {{$user_withdrawal->requested_amount_formatted ?: 0}}
                                    </td>

                                    <td>
                                        {{formatted_amount($user_withdrawal->requested_amount,'','',NO)}}
                                    </td>

                                    <td>
                                        {{ $user_withdrawal->payment_mode ?: tr('n_a')}}
                                    </td>

                                    <td>
                                        {{formatted_amount($user_withdrawal->paid_amount,'','',NO)}}
                                    </td>

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

                                    <td>
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                @if(in_array($user_withdrawal->status,[ WITHDRAW_INITIATED,WITHDRAW_ONHOLD]))

                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#paynowModal{{$i}}">
                                                    
                                                    <span class="nav-text">{{tr('paynow')}}</span>

                                                </a>

                                                <div class="dropdown-divider"></div>

                                                <a href="{{route('admin.user_withdrawals.reject',['user_withdrawal_id'=>$user_withdrawal->id])}}" class="dropdown-item" onclick="return confirm(&quot;{{tr('user_withdrawal_reject_confirmation')}}&quot;);">{{tr('reject')}}</a>

                                                @endif

                                                <div class="dropdown-divider"></div>


                                                <a href="{{route('admin.user_withdrawals.view',['user_withdrawal_id'=>$user_withdrawal->id])}}" class="dropdown-item">{{tr('view')}}</a>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $user_withdrawals->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>


@foreach($user_withdrawals as $i => $withdrawal_details)

<div id="paynowModal{{$i}}" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title pull-left">
                    <a href="{{route('admin.users.view' , ['user_id' => $withdrawal_details->user_id])}}"> {{ $withdrawal_details->user->name ?? tr('user_details_not_avail')}}
                    </a>
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('account_holder_name')}}</b>
                        <p>{{$withdrawal_details->billingAccount->account_holder_name ?? tr('not_available') }}</p>
                    </div>

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('account_number')}}</b>
                        <p>{{$withdrawal_details->billingAccount->account_number ?? tr('not_available') }}</p>
                    </div>

                </div>

                <div class="row">

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('bank_name')}}</b>
                        <p>{{$withdrawal_details->billingAccount->bank_name ?? tr('not_available') }}</p>
                    </div>

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('ifsc_code')}}</b>
                        <p>{{$withdrawal_details->billingAccount->ifsc_code ?? tr('not_available') }}</p>
                    </div>


                </div>

                <div class="row">

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('swift_code')}}</b>
                        <p>{{$withdrawal_details->billingAccount->swift_code ?? tr('not_available') }}</p>
                    </div>

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('created_at')}}</b>
                        <p>{{ common_date($withdrawal_details->created_at,Auth::guard('admin')->user()->timezone,'d M Y') }}</p>
                    </div>

                </div>

            </div>


            <div class="row">

                <div class="col-sm popup-label popup-left">
                    <b class="label-font">{{tr('requested_amount')}}</b>
                    <p>{{formatted_amount($withdrawal_details->requested_amount ?? '0.00','','',NO)}}</p>
                </div>

                <div class="col-sm popup-label"></div>

            </div>



            <div class="modal-footer"><br>

                <form class="forms-sample" action="{{ route('admin.user_withdrawals.paynow', ['user_withdrawal_id' => $withdrawal_details->id]) }}" method="GET" role="form">
                    @csrf

                    <input type="hidden" name="user_withdrawal_id" id="user_withdrawal_id" value="{{$withdrawal_details->id}}">

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
@endforeach

@endsection