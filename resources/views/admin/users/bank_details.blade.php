@extends('layouts.admin')

@section('title', tr('view_billing_account'))

@section('content-header', tr('users'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{tr('view_billing_account')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('view_billing_account')}}
                        @if($user) 
                        - 
                        <a href="{{route('admin.users.view',['user_id' => $user->id])}}">{{$user->name ?? tr('n_a')}}</a>

                        @endif
                    </h4>

                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        <form method="GET" action="{{route('admin.users.billing_accounts')}}">

                            <div class="row">

                                <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">
                                </div>

                                <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

                                    <div class="input-group">

                                        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id}}">

                                        <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('bank_search_placeholder')}}"> 

                                        <span class="input-group-btn">
                                            &nbsp

                                            <button type="submit" class="btn btn-default reset-btn">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                            </button>

                                            <a href="{{route('admin.users.billing_accounts',['user_id'=>$user->id ?? ''])}}" class="btn btn-default reset-btn">
                                                <i class="fa fa-eraser" aria-hidden="true"></i>
                                            </a>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </form><br>

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('route_number') }}</th>
                                    <th>{{ tr('account_number') }}</th>
                                    <th>{{ tr('first_name') }}</th>
                                    <th>{{ tr('last_name') }}</th>
                                    <th>{{ tr('bank_type') }}</th>
                                    <!-- <th>{{tr('ifsc_code')}}</th> -->
                                    <th>{{ tr('business_name') }}</th>
                                    <th>{{ tr('is_default') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($bank_details as $i => $bank_detail)

                                <tr>

                                    <td>{{ $i+$bank_details->firstItem() }}</td>

                                    <td class="white-space-nowrap">
                                        {{$bank_detail->route_number ?: tr('not_available')}}
                                    </td>

                                    <td>
                                        {{$bank_detail->account_number ?: tr('not_available')}}
                                    </td>

                                    <td class="white-space-nowrap">
                                        {{ $bank_detail->first_name ?: tr('not_available')}}
                                    </td>

                                    <td class="white-space-nowrap">
                                        {{$bank_detail->last_name ?: tr('not_available')}}
                                    </td>

                                    <td>
                                        @if($bank_detail->bank_type == BANK_TYPE_SAVINGS)

                                            <span class="btn btn-success btn-sm">{{ tr('saving') }}</span>

                                        @else

                                            <span class="btn btn-success btn-sm">{{ tr('checking') }}</span>

                                        @endif
                                    </td>

                                    <!-- <td>
                                        {{$bank_detail->ifsc_code ?: tr('not_available')}}
                                    </td> -->
                                    
                                    <td>
                                        {{$bank_detail->business_name ?: tr('not_available')}}
                                    </td>

                                    <td>
                                        @if($bank_detail->is_default == YES)

                                            <span class="btn btn-success btn-sm">{{ tr('yes') }}</span>

                                        @else

                                            <span class="btn btn-danger btn-sm">{{ tr('no') }}</span>

                                        @endif
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $bank_details->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
