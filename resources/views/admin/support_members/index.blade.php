@extends('layouts.admin')

@section('title', tr('support_members'))

@section('content-header', tr('support_member'))

@section('breadcrumb')


    
<li class="breadcrumb-item active"><a href="">{{ tr('support_member') }}</a></li>

<li class="breadcrumb-item">{{tr('view_support_member')}}</li>

@endsection

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_support_member') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.support_members.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_support_member') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <form method="GET" action="{{route('admin.support_members.index')}}">

                            <div class="row">

                                <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
                                    @if(Request::has('search_key'))
                                    <p class="text-muted">Search results for <b>{{Request::get('search_key')}}</b></p>
                                    @endif
                                </div>

                                <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">

                                    <select class="form-control select2" name="status">

                                        <option class="select-color" value="">{{tr('select_status')}}</option>

                                        <option class="select-color" value="{{SORT_BY_APPROVED}}" @if(Request::get('status') == SORT_BY_APPROVED && Request::get('status')!='' ) selected @endif>{{tr('approved')}}</option>

                                        <option class="select-color" value="{{SORT_BY_DECLINED}}" @if(Request::get('status') == SORT_BY_DECLINED && Request::get('status')!='' ) selected @endif>{{tr('declined')}}</option>

                                        <option class="select-color" value="{{SORT_BY_EMAIL_VERIFIED}}" @if(Request::get('status') == SORT_BY_EMAIL_VERIFIED && Request::get('status')!='' ) selected @endif>{{tr('verified')}}</option>

                                        <option class="select-color" value="{{SORT_BY_EMAIL_NOT_VERIFIED}}" @if(Request::get('status') == SORT_BY_EMAIL_NOT_VERIFIED && Request::get('status')!='' ) selected @endif>{{tr('unverified')}}</option>

                                    </select>

                                </div>

                                <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

                                    <div class="input-group">

                                        <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('support_member_search_placeholder')}}"> <span class="input-group-btn">
                                            &nbsp

                                            <button type="submit" class="btn btn-default">
                                                <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                                            </button>

                                            <a href="{{route('admin.support_members.index')}}" class="btn btn-default reset-btn">
                                                <span class="glyphicon glyphicon-search"> <i class="fa fa-eraser" aria-hidden="true"></i>
                                                </span>
                                            </a>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </form>
                        <br>

                        <table class="table table-striped table-bordered sourced-data table-responsive">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('email') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('verify') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($support_members as $i => $support_member) 

                                <tr>
                                    <td>{{ $i+$support_members->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.support_members.view' , ['support_member_id' => $support_member->id] )  }}">
                                            {{ $support_member->name }}
                                        </a>
                                    </td>

                                    <td>{{ $support_member->email }}<br>
                                        <span class="text-success">{{ $support_member->mobile ?: "-" }}</span>
                                    </td>

                                    <td>
                                        @if($support_member->status == APPROVED)
                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                                        @else
                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                        @endif
                                    </td>

                                    <td>
                                        
                                        @if($support_member->is_email_verified)
                                        <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.support_members.verify' , ['support_member_id' => $support_member->id]) }}">
                                            <i class="icon-close"></i> {{ tr('verify') }}
                                        </a>

                                        @else         

                                        <span class="btn btn-success btn-sm">{{ tr('verified') }}</span> 
                                        @endif
                                    </td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.support_members.view', ['support_member_id' => $support_member->id] ) }}">&nbsp;{{ tr('view') }}</a>

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.support_members.edit', ['support_member_id' => $support_member->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('support_member_delete_confirmation' , $support_member->name) }}&quot;);" href="{{ route('admin.support_members.delete', ['support_member_id' => $support_member->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($support_member->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.support_members.status' , ['support_member_id' => $support_member->id] )  }}" onclick="return confirm(&quot;{{ $support_member->name }} - {{ tr('support_member_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.support_members.status' , ['support_member_id' => $support_member->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                <div class="dropdown-divider"></div>

                                                @if($support_member->is_content_creator)

                                                <a class="dropdown-item" href="{{ route('admin.users.followers',['follower_id' => $support_member->id]) }}">&nbsp;{{ tr('followers') }}</a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.followings',['support_member_id' => $support_member->id]) }}">&nbsp;{{ tr('followings') }}</a>
                                                @endif

                                                <a class="dropdown-item" href="{{ route('admin.orders.index', ['support_member_id' => $support_member->id] ) }}">&nbsp;{{ tr('orders') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.post.payments', ['support_member_id' => $support_member->id] ) }}">&nbsp;{{ tr('post_payments') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.delivery_address.index', ['support_member_id' => $support_member->id] ) }}">&nbsp;{{ tr('delivery_address') }}</a>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $support_members->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection