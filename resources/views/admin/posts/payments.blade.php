@extends('layouts.admin') 

@section('title', tr('revenue_management')) 

@section('content-header', tr('payments')) 

@section('breadcrumb')
    
<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>

<li class="breadcrumb-item active">{{ tr('post_payments') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row ">

        <div class="col-12 ">

            <div class="card post-payment-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('post_payments') }} 
                        
                    @if($user)
                    - 
                    <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a>
                    @endif

                    @if($post)
                    - 
                    <a href="{{route('admin.posts.view',['post_id'=>$post->id ?? ''])}}">{{$post->unique_id ?? ''}}</a>
                    @endif
                    
                   </h4>

                   <div class="heading-elements">

                        <a href="{{ route('admin.post_payment.excel',['user_id'=>Request::get('user_id'),'search_key'=>Request::get('search_key'),'post_id'=>Request::get('post_id'),'file_format'=>'.csv']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to CSV</a>

                        <a href="{{ route('admin.post_payment.excel',['user_id'=>Request::get('user_id'),'search_key'=>Request::get('search_key'),'post_id'=>Request::get('post_id'),'file_format'=>'.xls']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLS</a>

                        <a href="{{ route('admin.post_payment.excel',['user_id'=>Request::get('user_id'),'search_key'=>Request::get('search_key'),'post_id'=>Request::get('post_id'),'file_format'=>'.xlsx']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLSX</a>
                        
                    </div>
                    
                </div>

                 <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('post_payments_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">
                    
                    <div class="table-responsive">
                        @include('admin.posts._payment_search')
                        
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('post_id')}}</th>
                                    <th >{{ tr('payment_id') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('admin_amount') }}</th>
                                    <th>{{ tr('user_amount') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($post_payments as $i => $post_payment)
                                <tr>
                                    <td>{{ $i+$post_payments->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $post_payment->user_id] )  }}">
                                        {{ $post_payment->user->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.posts.view' , ['post_id' => $post_payment->post_id] )  }}">
                                        {{$post_payment->postDetails->unique_id ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ $post_payment->payment_id }}
                                        <br>
                                        <br>
                                        <span class="text-gray">{{tr('date')}}: {{common_date($post_payment->paid_date, Auth::user()->timezone)}}</span>
                                    </td>

                                    <td>
                                        {{ $post_payment->paid_amount_formatted}}
                                    </td>


                                    <td>
                                        {{ $post_payment->admin_amount_formatted}}
                                    </td>

                                    <td>
                                        {{ $post_payment->user_amount_formatted}}
                                    </td>


                                    <td>
                                        <span class="badge badge-secondary">
                                        {{ $post_payment->payment_mode}}
                                        </span>
                                    </td>
                                        
                                    <td class="flex payments-action-left">
                                        
                                       <a href="{{route('admin.post.payments.view',['post_payment_id' => $post_payment->id])}}" class="btn btn-primary">{{tr('view')}}</a>&nbsp;
                                   
                                       <a href="{{route('admin.post_payments.send_invoice',['post_payment_id' => $post_payment->id])}}" class="btn btn-primary"><i class="fa fa-envelope"></i>&nbsp;{{tr('send_invoice')}}</a>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $post_payments->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection

@section('styles')
<style>
    .table th, .table td {
    padding: 0.75rem 1.5rem !important;
}
</style>
@endsection