@extends('layouts.admin')

@section('content-header', tr('payments'))

@section('breadcrumb')

<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('chat_asset_payments') }}</span>
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('chat_asset_payments') }}</h4>

                    <div class="heading-elements">

                        <a href="{{ route('admin.chat_asset_payment.excel',['from_user_id'=>Request::get('from_user_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.csv']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to CSV</a>

                        <a href="{{ route('admin.chat_asset_payment.excel',['from_user_id'=>Request::get('from_user_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.xls']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLS</a>

                        <a href="{{ route('admin.chat_asset_payment.excel',['from_user_id'=>Request::get('from_user_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.xlsx']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLSX</a>
                        
                    </div>

                </div>

                <div class="box box-outline-purple">
                
                    <div class="box-body">

                        @include('admin.users.chat._search')

                        <div class="table-responsive">

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('from_username')}}</th>
                                    <th>{{tr('to_username')}}</th>
                                    <th>{{tr('payment_id')}}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('admin_amount') }}</th>
                                    <th>{{ tr('user_amount') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($chat_asset_payments as $i => $chat_asset_payment)

                                <tr>
                                    <td>{{$i+$chat_asset_payments->firstItem()}}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $chat_asset_payment->from_user_id])}}"> {{ $chat_asset_payment->fromUser->name ?? tr('not_available')}}
                                        </a>
                                    </td>

                                    <td><a href="{{route('admin.users.view' , ['user_id' => $chat_asset_payment->to_user_id])}}"> {{ $chat_asset_payment->toUser->name ?? tr('not_available') }}</a></td>

                                    <td>
                                        <a href="{{ route('admin.chat_asset_payments.view', ['chat_asset_payment_id' => $chat_asset_payment->id] ) }}"> {{ $chat_asset_payment->payment_id ?: tr('not_available')}}
                                        </a>

                                        <br>
                                        <br>

                                        <span class="text-gray">{{tr('date')}}: {{common_date($chat_asset_payment->paid_date, Auth::user()->timezone)}}</span>
                                    </td>

                                    <td>{{ $chat_asset_payment->amount_formatted }}</td>

                                    <td>{{ $chat_asset_payment->admin_amount_formatted }}</td>

                                    <td>{{ $chat_asset_payment->user_amount_formatted }}</td>

                                    <td>{{ $chat_asset_payment->payment_mode }}</td>

                                    <td>

                                         <a class="btn btn-info" href="{{ route('admin.chat_asset_payments.view', ['chat_asset_payment_id' => $chat_asset_payment->id] ) }}">&nbsp;{{ tr('view') }}</a>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>
                        <div class="pull-right" id="paglink">{{ $chat_asset_payments->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection