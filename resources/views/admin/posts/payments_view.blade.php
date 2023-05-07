@extends('layouts.admin') 

@section('title', tr('payments')) 

@section('content-header', tr('payments')) 

@section('breadcrumb')


    
<li class="breadcrumb-item">
    <a href="{{route('admin.post.payments')}}">{{ tr('payments') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('post_payments') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('post_payments') }}</h4>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body box box-outline-info">

                        <div class="row">
                            
                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $post_payment->unique_id ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('post_id')}} </td>
                                            <td>{{ $post_payment->postDetails->unique_id ?? tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $post_payment->payment_id ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('paid_user_name')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $post_payment->user_id])}}">
                                                {{ $post_payment->user->name ?? tr('n_a')}}
                                                </a>
                                            </td>
                                        </tr>  

                                        <tr>
                                            <td>{{ tr('paid_amount') }}</td>
                                            <td>{{ $post_payment->paid_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('admin_amount') }}</td>
                                            <td>{{ $post_payment->admin_amount_formatted ?: 0}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('user_amount') }}</td>
                                            <td>{{ $post_payment->user_amount_formatted ?: 0}}</td>
                                        </tr>
                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">
                                
                                 <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $post_payment->payment_mode ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($post_payment->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status') }}</td>
                                            <td>
                                                @if($post_payment->status ==YES)

                                                    <span class="badge bg-success">{{tr('paid')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('not_paid')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('is_failed') }}</td>
                                            <td>
                                                @if($post_payment->is_failed ==YES)

                                                    <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        @if($post_payment->is_failed ==YES)
                                        <tr>
                                            <td>{{ tr('failed_reason') }}</td>
                                            <td>{{ $post_payment->failed_reason ?: tr('n_a')}}</td>
                                        </tr>
                                        @endif

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($post_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($post_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('post_content')}} </td>
                                            <td>
                                                <a href="{{ route('admin.posts.view', ['post_id' => $post_payment->post_id])}}">
                                                {!! $post_payment->postDetails->content  ?? tr('n_a') !!}
                                                </a>
                                            </td>
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