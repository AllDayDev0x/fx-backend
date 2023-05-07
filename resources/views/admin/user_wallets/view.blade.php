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

            <h4 class="card-title">{{ tr('view_user_wallets') }} - <a
                    href="{{route('admin.users.view',['user_id' => $user_wallet->user_id])}}">{{$user_wallet->user->name ?? tr('n_a')}}</a>
            </h4>

            <div class="heading-elements">

                <a href="{{route('admin.user_withdrawals', ['user_id' => $user_wallet->user_id])}}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('withdraw_requests') }}</a>          

            </div>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card bg-warning">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3>{{$user_wallet->total_formatted}}</h3>
                                        <span>{{tr('total')}}</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="icon-wallet font-large-2 white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card bg-success">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3>{{$user_wallet->used_formatted}}</h3>
                                        <span>{{tr('used')}}</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="icon-support white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card bg-info">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3>{{$user_wallet->onhold_formatted}}</h3>
                                        <span>{{tr('onhold')}}</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="icon-support white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card bg-danger">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3>{{$user_wallet->remaining_formatted}}</h3>
                                        <span>{{tr('remaining')}}</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="icon-pie-chart white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card bg-primary">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3>{{$user_wallet->referral_amount_formatted}}</h3>
                                        <span>{{tr('referral_balance')}}</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="icon-pie-chart white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <h5>{{tr('payment_history')}}</h5><br><br>
				<div class="col-md-12 box box-outline-info">
                    <div class="table-responsive">
					<table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

						<thead>
							<tr>
								<th>{{ tr('s_no') }}</th>
								<th>{{ tr('payment_id') }} </th>
								<th>{{ tr('payment_mode') }}</th>
								<th>{{ tr('amount') }}</th>
                                <th>{{ tr('message') }}</th>
								<th>{{ tr('status') }}</th>
                                <th>{{ tr('action') }} </th>
							</tr>
						</thead>

						<tbody>

							@if($user_wallet_payments->isNotEmpty())

    							@foreach($user_wallet_payments as $i => $user_wallet_payment)

        							<tr>
        								<td>{{ $i+$user_wallet_payments->firstItem() }}</td>

        								<td>
                                            <a href="{{route('admin.user_wallet_payments.view', ['payment_id' => $user_wallet_payment->id] )}}">{{ $user_wallet_payment->payment_id}}</a>
                                            <br>
                                            <br>
                                            <span class="text-gray">{{tr('date')}}: {{common_date($user_wallet_payment->paid_date, Auth::user()->timezone)}}</span>
                                        </td>

        								<td>{{ $user_wallet_payment->payment_mode }}</td>



        								<td>{{Setting::get('is_only_wallet_payment') ? formatted_amount($user_wallet_payment->user_token + $user_wallet_payment->admin_token) : formatted_amount($user_wallet_payment->user_amount + $user_wallet_payment->admin_amount)}}
                                            <br><br>
                                            <span class="text-gray"> Admin: {{$user_wallet_payment->admin_amount_formatted}}</span>
                                            <span class="text-gray"> User: {{$user_wallet_payment->user_amount_formatted}}</span>
                                        </td>

                                        <td>{{ $user_wallet_payment->message }}</td>

        								<td>
        									@if($user_wallet_payment->status == PAID)

        									<span class="btn btn-success btn-sm">{{ tr('paid') }}</span>
        									@else

        									<span class="btn btn-warning btn-sm">{{ tr('not_paid') }}</span>
        									@endif
        								</td>

                                        <td>  
                                           
                                            <a class="btn btn-info" href="{{route('admin.user_wallet_payments.view', ['payment_id' => $user_wallet_payment->id] )}}">
                                                        {{ tr('view') }}
                                            </a>

                                        </td>
        							</tr>

    							@endforeach

							@endif

						</tbody>

					</table>

					<div class="pull-right" id="paglink">{{ $user_wallet_payments->appends(request()->input())->links('pagination::bootstrap-4') }}
					</div>
                </div>
				</div>

            </div>

        </div>

    </div>

</div>

@endsection