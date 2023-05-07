@extends('layouts.admin') 

@section('content-header', tr('users'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index')}}">{{tr('user_referrals')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('user_referrals') }}</span>
    </li> 
           
@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('user_referrals') }}</h4>

                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        @include('admin.referrals._search')

                        <div class="table-responsive">

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('username')}}</th>
                                    <th>{{tr('referral_code')}}</th>
                                    <th>{{tr('total_referrals')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($referrals as $i => $referral)
                                      
                                    <tr>
                                        <td>{{$i+$referrals->firstItem()}}</td>

                                        <td><a href="{{route('admin.users.view' , ['user_id' => $referral->user_id])}}"> {{$referral->username ?: tr('n_a')}}
                                            </a></td>

                                        <td>  
                                            <a href="{{ route('admin.referrals.view', ['referral_id' => $referral->referral_code_id]) }}">{{$referral->referral_code ?: tr('n_a')}}</a>
                                        </td>

                                        <td>
                                            @if($referral->total_referrals_formatted > 0)
                                            <a href="{{ route('admin.users.index',['referral_code_id' => $referral->referral_code_id])}}">
                                                {{$referral->total_referrals_formatted ?? '-'}}
                                            </a>
                                            @else
                                                0
                                            @endif
                                        </td>


                                        <td>     

                                            <div class="template-demo">

                                                <button class="btn btn-info">
                                                       
                                                    <a href="{{ route('admin.referrals.view',['referral_id' => $referral->referral_code_id])}}" style="color:#fff">
                                                            {{tr('view')}}
                                                    </a>

                                                </button>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach
                                
                            </tbody>
                        
                        </table>
                        <div class="pull-right resp-float-unset" id="paglink">{{ $referrals->appends(request()->input())->links('pagination::bootstrap-4') }}</div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection

