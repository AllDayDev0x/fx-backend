@extends('layouts.admin')

@section('title', tr('support_members'))

@section('content-header', tr('support_members'))

@section('breadcrumb')

    
    <li class="breadcrumb-item"><a href="{{route('admin.support_members.index')}}">{{tr('support_members')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_support_members')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">

    <div id="support_member-profile">

        <div class="row">
  
            <div class="col-xl-12 col-lg-12">

                <div class="card">

                    <div class="card-header border-bottom border-gray">

                        <h4 class="card-title">{{tr('view_support_members')}}</h4>

                    </div>

                    <div class="card-content">

                        <div class="col-12">

                            <div class="card profile-with-cover">

                                <div class="media profil-cover-details w-100">

                                    <div class="media-left pl-2 pt-2">

                                        <a  class="profile-image">
                                          <img src="{{ $support_member->picture}}" alt="{{ $support_member->name}}" class="img-thumbnail img-fluid img-border height-100"
                                          alt="Card image">
                                        </a>
                                        
                                    </div>

                                    <div class="media-body pt-3 px-2">

                                        <div class="row">

                                            <div class="col">
                                                <h3 class="card-title">{{ $support_member->name }}</h3>
                                                <span class="text-muted">{{ $support_member->email }}</span>
                                            </div>

                                        </div>

                                    </div>
                                    
                                </div>

                                <nav class="navbar navbar-light navbar-profile align-self-end">
                                   
                                </nav>
                            </div>
                        </div>

                        <div class="table-responsive">

                            <table class="table table-xl mb-0">
                                <tr>
                                    <th>{{tr('support_membername')}}</th>
                                    <td>{{$support_member->name}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('email')}}</th>
                                    <td>{{$support_member->email}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('payment_mode')}}</th>
                                    <td>{{$support_member->payment_mode}}</td>
                                </tr>
                                
                                <tr>
                                    <th>{{tr('login_type')}}</th>
                                    <td>{{$support_member->login_by}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('device_type')}}</th>
                                    <td>{{$support_member->device_type}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('status')}}</th>
                                    <td>
                                        @if($support_member->status == APPROVED)

                                            <span class="badge badge-success">{{tr('approved')}}</span>
                                        @else
                                        
                                            <span class="badge badge-danger">{{tr('declined')}}</span>
                                            @endif
                                        
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('email_notification')}}</th>
                                    <td>
                                        @if($support_member->is_email_notification == YES) 

                                            <span class="badge badge-success">{{tr('yes')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('push_notification')}}</th>
                                    <td>
                                        @if($support_member->is_push_notification == YES) 

                                            <span class="badge badge-success">{{tr('yes')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                  <th>{{tr('created_at')}} </th>
                                  <td>{{$support_member->created_at}}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('updated_at')}} </th>
                                  <td>{{$support_member->updated_at}}</td>
                                </tr>
                                
                            </table>

                        </div>

                    </div>

                    <div class="card-footer">

                        <div class="card-title">
                            {{tr('action')}}
                        </div>

                        <div class="row">

                            @if(Setting::get('is_demo_control_enabled') == YES)

                            <div class="col-3">

                                <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="javascript:void(0)"> &nbsp;{{tr('edit')}}</a>

                            </div>

                            <div class="col-3">

                                <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" href="javascript:void(0)">&nbsp;{{tr('delete')}}</a>

                            </div>


                            @else

                            <div class="col-3">

                                <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.support_members.edit', ['support_member_id'=>$support_member->id] )}}"> &nbsp;{{tr('edit')}}</a>

                            </div>

                            <div class="col-3">

                                <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('support_member_delete_confirmation' , $support_member->name)}}&quot;);" href="{{route('admin.support_members.delete', ['support_member_id'=> $support_member->id] )}}">&nbsp;{{tr('delete')}}</a>

                            </div>

                            @endif

                            <div class="col-3">
                                
                                <a href="{{route('admin.delivery_address.index',['support_member_id' => $support_member->id])}}" class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1">{{tr('delivery_address')}}</a>

                            </div>

                            <div class="col-3">

                                @if($support_member->status == APPROVED)
                                     <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.support_members.status' ,['support_member_id'=> $support_member->id] )}}" onclick="return confirm(&quot;{{$support_member->name}} - {{tr('support_member_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                                @else

                                    <a  class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.support_members.status' , ['support_member_id'=> $support_member->id] )}}">&nbsp;{{tr('approve')}}</a> 
                                @endif
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-3">
                                
                                <a href="{{route('admin.post.payments',['support_member_id' => $support_member->id])}}" class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1">{{tr('post_payments')}}</a>

                            </div>

                            <div class="col-3">
                                
                                <a href="{{route('admin.orders.index',['support_member_id' => $support_member->id])}}" class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1">{{tr('orders')}}</a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
  
    
@endsection

@section('scripts')

@endsection
