@extends('layouts.admin')

@section('content-header', tr('tip_payments'))

@section('breadcrumb')

<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>

<li class="breadcrumb-item">
    <a href="{{ route('admin.user_tips.index') }}">{{ tr('tip_payments') }}</a>
</li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('view_tip_payment') }}</span>
</li>

@endsection

@section('content')

<section class="content">
    
    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_tip_payment') }}</h4>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body box box-outline-info">

                        <div class="row">

                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>

                                        <tr>
                                            <td>{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $user_tip->unique_id ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('from_username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view' , ['user_id' => $user_tip->user_id])}}">
                                                    {{ $user_tip->from_username ?: tr('not_available')}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('to_username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id'=>$user_tip->to_user_id])}}">
                                                    {{ $user_tip->to_username ?: tr('not_available')}}
                                                </a>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('post_id')}} </td>
                                            <td>
                                                <a href="{{route('admin.posts.view',['post_id'=>$user_tip->post->id ?? ''])}}">
                                                    {{ $user_tip->post->unique_id ?? tr('not_available')}}
                                                </a>

                                            </td>
                                        </tr>


                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $user_tip->payment_id ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{Setting::get('is_only_wallet_payment') ? tr('tip_token') : tr('tip_amount')}} </td>
                                            <td>{{ $user_tip->amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('admin_amount')}} </td>
                                            <td>{{ $user_tip->admin_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('user_amount')}} </td>
                                            <td>{{ $user_tip->user_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $user_tip->payment_mode ?: tr('n_a')}}</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($user_tip->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status') }}</td>
                                            <td>
                                                @if($user_tip->status ==YES)

                                                <span class="badge bg-success">{{tr('paid')}}</span>

                                                @else
                                                <span class="badge bg-danger">{{tr('not_paid')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($user_tip->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($user_tip->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('message')}} </td>
                                            <td>{{$user_tip->message ?: tr('n_a')}}</td>
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