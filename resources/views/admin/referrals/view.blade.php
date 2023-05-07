@extends('layouts.admin') 

@section('content-header', tr('user_referrals'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index')}}">{{tr('user_referrals')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_user_referrals') }}</span>
    </li> 
           
@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_user_referrals') }}</h4>
    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered table-striped tab-content">
                                    <tbody>
                                        <tr>
                                            <td>{{ tr('referral_code') }}</td>
                                            <td>{{$referrals_code_details->referral_code ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('username') }}</td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id' => $referrals_code_details->user_id])}}">{{$referrals_code_details->username ?: tr('n_a')}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('no_joined_users') }}</td>
                                            <td>{{$referrals_code_details->total_referrals}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('referral_earnings') }}</td>
                                            <td>{{$referrals_code_details->referral_earnings_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('referee_earnings') }}</td>
                                            <td>{{$referrals_code_details->referee_earnings_formatted}}</td>
                                        </tr>

                                          <tr>
                                            <td>{{ tr('total') }}</td>
                                            <td>{{$referrals_code_details->total_formatted}}</td>
                                        </tr>

                                          <tr>
                                            <td>{{ tr('used') }}</td>
                                            <td>{{$referrals_code_details->used_formatted}}</td>
                                        </tr>

                                          <tr>
                                            <td>{{ tr('remaining') }}</td>
                                            <td>{{$referrals_code_details->remaining_formatted}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                               <div><h6><strong>{{tr('referral_users')}} :</strong></h6></div>
                               <div class="table-responsive">
                                    <table id="dataTable" class="table table-striped table-bordered sourced-data">
                                    <thead>
                                        <tr>
                                            <th>{{tr('username')}}</th>
                                            <th>{{tr('referral_code')}}</th>
                                            <th>{{tr('device_type')}}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($referrals_user_details as $i => $referral)
                                              <tr>
                                                <td>
                                                    <a href="{{route('admin.users.view',['user_id' => $referral->user_id])}}">
                                                    {{$referral->username}}
                                                    </a>
                                                </td>

                                                <td>{{$referral->referral_code}}</td>

                                                <td>{{$referral->device_type}}</td>
                                            </tr>

                                        @endforeach
                                        
                                    </tbody>
                                
                                </table>
                               </div>

                            </div>
                            
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection

