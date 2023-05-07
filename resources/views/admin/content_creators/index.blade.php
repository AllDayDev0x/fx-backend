@extends('layouts.admin')

@section('title', tr('content_creators'))

@section('content-header', tr('content_creators'))

@section('breadcrumb')

 
    
<li class="breadcrumb-item active">
    <a href="{{route('admin.content_creators.index')}}">{{ tr('content_creators') }}</a>
</li>

<li class="breadcrumb-item">{{ tr('view_content_creators') }}</li>

@endsection

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_content_creators') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.content_creators.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_content_creator') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.content_creators._search')

                        <table class="table table-striped table-bordered sourced-data table-responsive">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('email') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('verify') }}</th>
                                    <th>{{ tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($content_creators as $i => $content_creator_details)
                                <tr>
                                    <td>{{ $i+$content_creators->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.content_creators.view' , ['user_id' => $content_creator_details->id] )  }}">
                                            {{ $content_creator_details->name }}
                                        </a>
                                    </td>

                                    <td>{{ $content_creator_details->email }}
                                        <br>
                                        <span class="text-success">{{ $content_creator_details->mobile ?: "-" }}</span>
                                    </td>

                                    <td>
                                        @if($content_creator_details->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>

                                    <td>
                                        @if($content_creator_details->is_email_verified == CONTENT_CREATOR_EMAIL_NOT_VERIFIED)

                                        <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.content_creators.verify' , ['user_id' => $content_creator_details->id]) }}">
                                            <i class="icon-close"></i> {{ tr('verify') }}
                                        </a>

                                        @else

                                        <span class="btn btn-success btn-sm">{{ tr('verified') }}</span> @endif
                                    </td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.content_creators.view', ['user_id' => $content_creator_details->id] ) }}">&nbsp;{{ tr('view') }}</a>

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.content_creators.edit', ['user_id' => $content_creator_details->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('content_creator_delete_confirmation' , $content_creator_details->name) }}&quot;);" href="{{ route('admin.content_creators.delete', ['user_id' => $content_creator_details->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($content_creator_details->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.content_creators.status' , ['user_id' => $content_creator_details->id] )  }}" onclick="return confirm(&quot;{{ $content_creator_details->name }} - {{ tr('content_creator_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.content_creators.status' , ['user_id' => $content_creator_details->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif
                                                <hr>


                                                <a class="dropdown-item" href="{{ route('admin.users.followers',['follower_id' => $content_creator_details->id]) }}">&nbsp;{{ tr('followers') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.users.followings',['user_id' => $content_creator_details->id]) }}">&nbsp;{{ tr('followings') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.posts.index', ['user_id' => $content_creator_details->id] ) }}">&nbsp;{{ tr('posts') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.user_products.index', ['user_id' => $content_creator_details->id] ) }}">&nbsp;{{ tr('products') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.user_wallets.view', ['user_id' => $content_creator_details->id] ) }}">&nbsp;{{ tr('wallets') }}</a>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $content_creators->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection