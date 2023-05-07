@extends('layouts.admin')

@section('content-header', tr('payments'))

@section('breadcrumb')

<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('subscription_payments') }}</span>
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card user-subscription-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">
                        {{ tr('subscription_payments') }} 

                        @if(Request::get('from_user_id'))

                        -
                        <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a>

                        @endif

                    </h4>

                    <div class="heading-elements">

                        <a href="{{ route('admin.subscription_payment.excel',['from_user_id'=>Request::get('from_user_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.csv']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to CSV</a>

                        <a href="{{ route('admin.subscription_payment.excel',['from_user_id'=>Request::get('from_user_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.xls']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLS</a>

                        <a href="{{ route('admin.subscription_payment.excel',['from_user_id'=>Request::get('from_user_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.xlsx']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLSX</a>
                        
                    </div>

                </div>

                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('subscription_payments_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">
                    
                    <div class="table-responsive">
                        @include('admin.users.subscriptions._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('from_username')}}</th>
                                    <th>{{tr('to_username')}}</th>
                                    <th>{{tr('payment_id')}}</th>
                                    <th>{{tr('plan')}}</th>
                                    <th>{{tr('subscription_amount')}}</th>
                                    <th>{{tr('admin_amount')}}</th>
                                    <th>{{tr('user_amount')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th class="text-center">{{tr('invoice')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($user_subscriptions as $i => $subscription)

                                <tr>
                                    <td>{{$i+$user_subscriptions->firstItem()}}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $subscription->from_user_id])}}"> {{ $subscription->from_username ?:tr('not_available')}}
                                        </a>
                                    </td>

                                    <td><a href="{{route('admin.users.view' , ['user_id' => $subscription->to_user_id])}}"> {{ $subscription->to_username ?:tr('not_available') }}</a></td>

                                    <td>
                                        {{ $subscription->payment_id ?? tr('n_a')}}

                                        <br>
                                        <br>
                                        <span class="text-gray">{{tr('date')}}: {{common_date($subscription->paid_date, Auth::user()->timezone)}}</span>

                                    </td>

                                    <td>{{ substr($subscription->plan_text_formatted, 0, -1) ?? tr('n_a') }}</td>

                                    <td>{{ $subscription->amount_formatted ?: 0}}</td>

                                    <td>{{ $subscription->admin_amount_formatted ?: 0}}</td>

                                    <td>{{ $subscription->user_amount_formatted ?: 0}}</td>

                                    <td>

                                        @if($subscription->status == APPROVED)

                                        <span class="badge bg-success">{{ tr('paid') }} </span>

                                        @else

                                        <span class="badge bg-danger">{{ tr('not_paid') }} </span>

                                        @endif

                                    </td>

                                    <td>

                                        <a href="{{route('admin.subscription_payments.send_invoice',['user_subscription_id' => $subscription->id])}}" class="btn btn-primary"><i class="fa fa-envelope"></i>&nbsp;{{tr('send_invoice')}}</a>


                                    </td>

                                    <td>

                                        <a class="btn btn-info" href="{{ route('admin.user_subscriptions.view', ['subscription_id' => $subscription->id] ) }}">&nbsp;{{ tr('view') }}</a>

                                    </td>
                                       
                                </tr>

                                @endforeach

                            </tbody>

                        </table>
                        <div class="pull-right resp-float-unset" id="paglink">{{ $user_subscriptions->appends(request()->input())->links('pagination::bootstrap-4') }}</div>


                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection