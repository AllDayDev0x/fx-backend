@extends('layouts.admin') 

@section('title', tr('audio_call_payments')) 

@section('content-header', tr('audio_call_payments')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.audio_call_payments.index')}}">{{ tr('audio_call_payments') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_audio_call_payments') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card post-payment-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{ tr('view_audio_call_payments') }}

                        @if(Request::get('user_id'))
                        - 
                        <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a>
                        @endif

                    </h4>

                    <div class="heading-elements">

                        <a href="{{ route('admin.audio_call_payment.excel',['user_id'=>Request::get('user_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.csv']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to CSV</a>

                        <a href="{{ route('admin.audio_call_payment.excel',['user_id'=>Request::get('user_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.xls']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLS</a>

                        <a href="{{ route('admin.audio_call_payment.excel',['user_id'=>Request::get('user_id'),'search_key'=>Request::get('search_key'),'status'=>Request::get('status'),'file_format'=>'.xlsx']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLSX</a>
                        
                    </div>
                    
                </div>

                 <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('audio_call_payments_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                    <div class="box-body">

                        <div class="table-responsive">

                            @include('admin.revenues.audio_call_payments._search')

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                
                                <thead>
                                    <tr>
                                        <th>{{ tr('s_no') }}</th>
                                        <th>{{ tr('payment_id')}}</th>
                                        <th>{{ tr('username') }}</th>
                                        <th>{{ tr('model') }}</th>
                                        <th>{{ tr('amount') }}</th>
                                        <th>{{ tr('admin_amount') }}</th>
                                        <th>{{ tr('user_amount') }}</th>
                                        <th>{{ tr('status') }}</th>
                                        <th>{{ tr('action') }}</th>
                                    </tr>
                                </thead>
                               
                                <tbody>

                                    @foreach($audio_call_payments as $i => $audio_call_payment)
                                    <tr>
                                        <td>{{ $i+$audio_call_payments->firstItem() }}</td>

                                        <td> <a href="{{ route('admin.audio_call_payments.view', ['audio_call_payment_id' => $audio_call_payment->id] ) }}">{{$audio_call_payment->payment_id ?: tr('n_a')}}</a>

                                            <br>
                                            <br>
                                            <span class="text-gray">{{tr('date')}}: {{common_date($audio_call_payment->paid_date, Auth::user()->timezone)}}</span>
                                        </td>

                                        <td>
                                            <a href="{{  route('admin.users.view' , ['user_id' => $audio_call_payment->user_id] )  }}">
                                            {{ $audio_call_payment->user->name ?? tr('n_a') }}
                                            </a>
                                        </td>

                                        <td>
                                            <a href="{{  route('admin.users.view' , ['user_id' => $audio_call_payment->model_id] )  }}">
                                            {{ $audio_call_payment->model->name ?? tr('n_a') }}
                                            </a>
                                        </td>

                                        <td>
                                            {{ $audio_call_payment->paid_amount_formatted ?: 0}}
                                        </td>

                                        <td>
                                            {{ $audio_call_payment->admin_amount_formatted}}
                                        </td>

                                        <td>
                                            {{ $audio_call_payment->user_amount_formatted}}
                                        </td>

                                        <td>
                                            @if($audio_call_payment->status == PAID)

                                                <span class="btn btn-success btn-sm">{{ tr('paid') }}</span>
                                            @else

                                                <span class="btn btn-warning btn-sm">{{ tr('not_paid') }}</span>
                                            @endif
                                        </td>


                                        <td>

                                            <a class="btn btn-primary" href="{{ route('admin.audio_call_payments.view', ['audio_call_payment_id' => $audio_call_payment->id] ) }}">&nbsp;{{ tr('view') }}</a> 
                                        
                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>
                            
                            </table>

                            <div class="pull-right" id="paglink">{{ $audio_call_payments->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection