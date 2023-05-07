@extends('layouts.admin') 

@section('title', tr('view_promo_code'))

@section('content-header', tr('promo_code'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.promo_codes.index')}}">{{tr('promo_code')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_promo_code')}}</span>
    </li>
           
@endsection  

@section('content')

    <section class="content">

        <div class="row">

            <div class="col-xl-12 col-lg-12">

                <div class="card user-profile-view-sec">

                    <div class="card-header border-bottom border-gray">

                        <h4 class="card-title">{{tr('view_users')}}</h4>

                    </div>

                    <div class="card-content">
                        
                        <div class="user-view-padding">
                            <div class="row"> 

                                <div class=" col-xl-6 col-lg-6 col-md-12">
                                    <div class="table-responsive">

                                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                            <tr >
                                                <th style="border-top: 0">{{tr('title')}}</th>
                                                <td style="border-top: 0">{{$promo_code->title ?: tr('n_a')}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('promo_code')}}</th>
                                                <td>{{$promo_code->promo_code ?: tr('n_a')}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('amount_type')}}</th>

                                                <td>
                                                @if($promo_code->amount_type == PERCENTAGE)

                                                    <span class="badge badge-success">{{tr('percentage_amount')}}</span>

                                                @else

                                                    <span class="badge badge-danger">{{tr('absoulte_amount')}}</span>

                                                @endif
                                                </td>

                                            </tr>

                                            <tr>
                                                <th>{{tr('amount')}}</th>

                                                <td>
                                                @if($promo_code->amount_type == PERCENTAGE)

                                                    {{$promo_code->amount ?: 0}} %

                                                @else

                                                    {{formatted_amount($promo_code->amount) ?: 0}}

                                                @endif
                                                </td>

                                            </tr>
                                            
                                            <tr>
                                                <th>{{tr('status')}}</th>
                                                <td>
                                                    @if($promo_code->status == APPROVED)

                                                    <span class="badge badge-success">{{tr('approved')}}</span>

                                                    @else
                                                    <span class="badge badge-danger">{{tr('declined')}}</span>

                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('user_wallet_balance')}}</th>
                                                <td>
                                                    {{$user->userWallets->remaining_formatted ?? formatted_amount(0.00)}}
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('per_users_limit')}}</th>
                                                <td>
                                                    <a href="#">
                                                    {{$promo_code->per_users_limit ?? 0}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('no_of_users_limit')}}</th>
                                                <td>
                                                    <a href="#">
                                                    {{$promo_code->no_of_users_limit ?? 0}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('no_of_time_used')}}</th>
                                                <td>
                                                    <a href="#">
                                                    {{$promo_code_used ?? 0}}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('created_at')}} </th>
                                                <td>{{common_date($promo_code->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('updated_at')}} </th>
                                                <td>{{common_date($promo_code->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('start_date')}} </th>
                                                <td>{{common_date($promo_code->start_date , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('expiry_date')}} </th>
                                                <td>{{common_date($promo_code->expiry_date , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>


                                        </table>

                                    </div>

                                </div>

                                <div class=" col-xl-6 col-lg-6 col-md-12">
                                    

                                    @if(Setting::get('is_demo_control_enabled') == NO )

                                        <a href="{{ route('admin.promo_codes.edit', ['promo_code_id' => $promo_code->id] ) }}" class="btn btn-primary btn-block">
                                            {{tr('edit')}}
                                        </a>

                                        <a onclick="return confirm(&quot;{{tr('promo_code_delete_confirmation' , $promo_code->title)}}&quot;);" href="{{ route('admin.promo_codes.delete',['promo_code_id' => $promo_code->id] ) }}"  class="btn btn-danger btn-block">
                                            {{tr('delete')}}
                                        </a>

                                    @else
                                        <a href="javascript:;" class="btn btn-primary btn-block">{{tr('edit')}}</a>

                                        <a href="javascript:;" class="btn btn-danger btn-block">{{tr('delete')}}</a>

                                    @endif

                                    @if($promo_code->status == APPROVED)

                                        <a class="btn btn-danger btn-block" href="{{ route('admin.promo_codes.status', ['promo_code_id' => $promo_code->id] ) }}" 
                                        onclick="return confirm(&quot;{{$promo_code->title}} - {{tr('promo_code_decline_confirmation')}}&quot;);"> 
                                            {{tr('decline')}}
                                        </a>

                                    @else

                                        <a class="btn btn-success btn-block" href="{{ route('admin.promo_codes.status', ['promo_code_id' => $promo_code->id] ) }}">
                                            {{tr('approve')}}
                                        </a>
                                                               
                                    @endif

                                    <hr>
                                    <div class="box box-outline-purple">

                                        <div class="box-body">

                                            <div class="card-header border-bottom border-gray">

                                                <h4 class="card-title">{{tr('description')}}</h4>

                                            </div><br>
                                            <div class="card-content ml-4">
                                                
                                                {{ $promo_code->description ?: tr('n_a') }}

                                            </div><br>

                                        </div>

                                    </div>


                                </div>

                            </div>

                        </div>


                    </div>
                    
                <!-- Card -->
            </div>
            <!-- Card group -->

        </div>

    </section>

@endsection