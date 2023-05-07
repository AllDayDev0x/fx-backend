@extends('layouts.admin')

@section('content-header', tr('chat_asset_payment'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.chat_asset_payments.index') }}">{{ tr('payments') }}</a>
</li>

<li class="breadcrumb-item">
    <a href="{{ route('admin.chat_asset_payments.index') }}">{{ tr('chat_asset_payments') }}</a>
</li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('view_chat_asset_payment') }}</span>
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_chat_asset_payment') }}</h4>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <table class="table table-bordered table-striped tab-content">

                                    <tbody>

                                        <tr>
                                            <td>{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $chat_asset_payment->unique_id ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('from_username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id'=>$chat_asset_payment->from_user_id ?? ''])}}">
                                                    {{ $chat_asset_payment->fromUser->name ?? tr('not_available')}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('to_username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id'=>$chat_asset_payment->to_user_id ?? ''])}}">
                                                    {{ $chat_asset_payment->toUser->name ?? tr('not_available')}}
                                                </a>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $chat_asset_payment->payment_id ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('amount')}} </td>
                                            <td>{{ $chat_asset_payment->amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('admin_amount')}} </td>
                                            <td>{{ $chat_asset_payment->admin_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('user_amount')}} </td>
                                            <td>{{ $chat_asset_payment->user_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $chat_asset_payment->payment_mode ?: tr('n_a')}}</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">

                                <table class="table table-bordered table-striped tab-content">

                                    <tbody>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($chat_asset_payment->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('is_failed') }}</td>
                                            <td>
                                                @if($chat_asset_payment->is_failed ==YES)

                                                <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else
                                                <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('failed_reason') }}</td>
                                            <td>{{ $chat_asset_payment->failed_reason ?: tr('not_available')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($chat_asset_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($chat_asset_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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