@extends('layouts.admin') 

@section('title', tr('revenue_management')) 

@section('content-header', tr('revenue_management')) 

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.user_wallets.index')}}">{{ tr('revenue_management') }}</a>
</li>

<li class="breadcrumb-item ">{{tr('wallets')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card user-wallet-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('wallets')}}</h4>
                    
                </div>
                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('wallet_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">
                    
                    <div class="table-responsive">

                        @include('admin.user_wallets._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('wallet_id') }} </th> 
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('total_balance') }}</th>
                                    <th>{{ tr('onhold') }}</th>
                                    <th>{{ tr('used') }}</th>
                                    <th>{{ tr('remaining') }}</th>
                                    @if(Setting::get('is_referral_enabled'))
                                    <th>{{ tr('referral_balance') }}</th>
                                    @endif
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($user_wallets as $i => $user_wallet)
                                <tr>
                                    <td>{{ $i+$user_wallets->firstItem() }}</td>

                                    <td> <a href="{{ route('admin.user_wallets.view', ['user_id' => $user_wallet->user_id] ) }}">{{ $user_wallet->unique_id}}</a></td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $user_wallet->user_id] )  }}">
                                        {{ $user_wallet->user->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $user_wallet->total_formatted }}</td>

                                    <td>
                                        {{ $user_wallet->onhold_formatted}}
                                    </td>

                                    <td>
                                        {{ $user_wallet->used_formatted}}
                                    </td>

                                    <td>
                                        {{ $user_wallet->remaining_formatted}}
                                    </td>

                                    @if(Setting::get('is_referral_enabled'))
                                        <td>{{ $user_wallet->referral_amount_formatted}}</td>
                                    @endif

                                    <td>
                                    
                                        <a class="btn btn-info" href="{{ route('admin.user_wallets.view', ['user_id' => $user_wallet->user_id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $user_wallets->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection