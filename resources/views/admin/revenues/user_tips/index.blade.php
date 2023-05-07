@extends('layouts.admin')

@section('content-header', tr('payments'))

@section('breadcrumb')

<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('tip_payments') }}</span>
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card user-tips-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('tip_payments') }} 

                    @if(Request::get('user_id'))
                    - 
                    <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a>
                    @endif

                    @if(Request::get('post_id'))
                    - 
                    <a href="{{route('admin.posts.view',['post_id'=>$post->id ?? ''])}}">{{$post->unique_id ?? ''}}</a>
                    @endif
                    
                    </h4>

                    <div class="heading-elements">


                        <a href="{{ route('admin.tip_payment.excel',['user_id'=>Request::get('user_id'),'search_key'=>Request::get('search_key'),'post_id'=>Request::get('post_id'),'file_format'=>'.csv']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to CSV</a>

                        <a href="{{ route('admin.tip_payment.excel',['user_id'=>Request::get('user_id'),'search_key'=>Request::get('search_key'),'post_id'=>Request::get('post_id'),'file_format'=>'.xls']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLS</a>

                        <a href="{{ route('admin.tip_payment.excel',['user_id'=>Request::get('user_id'),'search_key'=>Request::get('search_key'),'post_id'=>Request::get('post_id'),'file_format'=>'.xlsx']) }}" class="btn btn-primary resp-mrg-btm-xs">Export to XLSX</a>
                        

                    </div>
                    
                </div>

                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>{{tr('tips_payments_notes')}}</li>
                            </ul>
                        <p></p>
                    </div>
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">
                    
                    <div class="table-responsive">

                        @include('admin.revenues.user_tips._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('from_username')}}</th>
                                    <th>{{tr('to_username')}}</th>
                                    <th>{{tr('post_id')}}</th>
                                    <th>{{tr('payment_id')}}</th>
                                    <!-- <th>{{tr('tip_token')}}</th> -->
                                    <th>{{Setting::get('is_only_wallet_payment') ? tr('tip_token') : tr('tip_amount')}}</th>
                                    <th>{{tr('admin_amount')}}</th>
                                    <th>{{tr('user_amount')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($user_tips as $i => $tips)

                                <tr>
                                    <td>{{$i+$user_tips->firstItem()}}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $tips->user_id])}}"> {{ $tips->from_username ?:tr('not_available')}}
                                        </a>
                                    </td>

                                    <td><a href="{{route('admin.users.view' , ['user_id' => $tips->to_user_id])}}"> {{ $tips->to_username ?:tr('not_available') }}</a></td>

                                    <td>
                                        @if($tips->post && $tips->post->unique_id)
                                        <a href="{{route('admin.posts.view',['post_id'=>$tips->post->id ?? ''])}}">
                                        {{ $tips->post->unique_id}}
                                        </a>
                                        @else
                                        {{tr('not_available') }}
                                        @endif

                                        <!-- <br>
                                        <br>
                                        <span class="text-gray">{{tr('date')}}: {{common_date($tips->paid_date, Auth::user()->timezone)}}</span> -->
                                    </td>

                                    <td>{{$tips->payment_id ?: 0}}</td>

                                    <!-- <td>{{$tips->token ?: 0}}</td> -->

                                    <td>{{ $tips->amount_formatted }}</td>

                                    <td>{{ $tips->admin_amount_formatted }}</td>

                                    <td>{{ $tips->user_amount_formatted }}</td>

                                    <td>

                                        @if($tips->status == APPROVED)

                                        <span class="badge bg-success">{{ tr('paid') }} </span>

                                        @else

                                        <span class="badge bg-danger">{{ tr('not_paid') }} </span>

                                        @endif

                                    </td>


                                    <td>

                                       <a class="btn btn-info" href="{{ route('admin.user_tips.view', ['user_tip_id' => $tips->id] ) }}">&nbsp;{{ tr('view') }}</a>
                                           
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>
                        <div class="pull-right resp-float-unset" id="paglink">{{ $user_tips->appends(request()->input())->links('pagination::bootstrap-4') }}</div>


                    </div>

                </div>
                
                </div>

            </div>

        </div>

    </div>

</section>
@endsection