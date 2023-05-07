@extends('layouts.admin') 

@section('content-header', tr('subscription_payments')) 

@section('breadcrumb')


    
<li class="breadcrumb-item">
    <a href="{{route('admin.subscription_payments.index')}}">{{ tr('subscription_payments') }}</a>
</li>

<li class="breadcrumb-item active">{{tr('view_subscription_payments')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_subscription_payments') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="row">
                            
                            <div class="col-md-6">

                                <table class="table table-bordered table-striped tab-content table-responsive-sm">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $subscription_payment->unique_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $subscription_payment->payment_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('plan')}} </td>
                                            <td>{{ $subscription_payment->plan_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('subscription')}} </td>
                                            <td><a href="{{route('admin.subscriptions.view',['subscription_id' => $subscription_payment->subscription_id])}}">{{ $subscription_payment->subscription->title ?? "-"}}</a></td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $subscription_payment->payment_mode}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('user')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $subscription_payment->user_id])}}">
                                                {{ $subscription_payment->user->name ?? "-"}}
                                                </a>
                                            </td>
                                        </tr> 

                                        <tr>
                                            <td>{{ tr('amount') }}</td>
                                            <td>{{ $subscription_payment->amount_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('is_current_subscription') }}</td>
                                            <td>
                                                @if($subscription_payment->is_current_subscription ==YES)

                                                    <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">
                                
                                <table class="table table-bordered table-striped tab-content table-responsive-sm">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('expiry_date') }}</td>
                                            <td>{{common_date($subscription_payment->expiry_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($subscription_payment->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status') }}</td>
                                            <td>
                                                @if($subscription_payment->status ==YES)

                                                    <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('is_cancelled') }}</td>
                                            <td>
                                                @if($subscription_payment->is_cancelled ==YES)

                                                    <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('cancel_reason') }}</td>
                                            <td>{{ $subscription_payment->cancel_reason}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($subscription_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($subscription_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection