@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{$title ?? tr('view_users')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">
        <div class="col-12">
        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{$title ?? tr('view_users')}}

                    @if($category)
                        - 
                        <a href="{{route('admin.categories.view' , ['category_id' => $category->id])}}">{{$category->name ?? tr('n_a')}}</a>

                    @endif

                </h4>

                <div class="heading-elements">

                    <div class="float-right ml-2">

                        @if($users->count() >= 1)
                        <a class="btn btn-primary dropdown-toggle bulk-action-dropdown resp-mrg-btm-xs" href="#" id="dropdownMenuOutlineButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-plus"></i> {{tr('bulk_action')}}
                        </a>
                        @endif

                        <div class="dropdown-menu float-right" aria-labelledby="dropdownMenuOutlineButton2">

                            <a class="dropdown-item action_list" href="#" id="bulk_delete">
                                {{tr('delete')}}
                            </a>

                            <a class="dropdown-item action_list" href="#" id="bulk_approve">
                                {{ tr('approve') }}
                            </a>

                            <a class="dropdown-item action_list" href="#" id="bulk_decline">
                                {{ tr('decline') }}
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_user') }}</a>

                    
                    <div class="float-right ml-2">

                        <a class="btn btn-primary dropdown-toggle bulk-action-dropdown resp-mrg-btm-xs" href="#" id="export_to_excel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Export to Excel
                        </a>

                        <div class="dropdown-menu float-right" aria-labelledby="export_to_excel">

                            <a class="dropdown-item action_list" href="{{ route('admin.users.excel',['downloadexcel'=>'excel','status'=>Request::get('status'),'search_key'=>Request::get('search_key'),'account_type'=>Request::get('account_type'),'file_format'=>'.csv']) }}">
                                Export to CSV
                            </a>

                            <a class="dropdown-item action_list" href="{{ route('admin.users.excel',['downloadexcel'=>'excel','status'=>Request::get('status'),'search_key'=>Request::get('search_key'),'account_type'=>Request::get('account_type'),'file_format'=>'.xls']) }}">
                                Export to XLS
                            </a>

                            <a class="dropdown-item action_list" href="{{ route('admin.users.excel',['downloadexcel'=>'excel','status'=>Request::get('status'),'search_key'=>Request::get('search_key'),'account_type'=>Request::get('account_type'),'file_format'=>'.xlsx']) }}">
                                Export to XLSX
                            </a>
                        </div>
                    </div>

                    <div class="bulk_action">

                        <form action="{{route('admin.users.bulk_action')}}" id="users_form" method="POST" role="search">

                            @csrf

                            <input type="hidden" name="action_name" id="action" value="">

                            <input type="hidden" name="selected_users" id="selected_ids" value="">

                            <input type="hidden" name="page_id" id="page_id" value="{{ (request()->page) ? request()->page : '1' }}">

                        </form>

                    </div>

                </div>

            </div>
            @if(Request::get('account_type') == 1)
            <div class="box-body">
                <div class="callout bg-pale-secondary">
                    <h4>{{tr('notes')}}</h4>
                    <p>
                        </p><ul>
                            <li>{{tr('premium_users_notes')}}</li>
                        </ul>
                    <p></p>
                </div>
            </div>

            @elseif(Request::get('account_type') == "0")
             <div class="box-body">
                <div class="callout bg-pale-secondary">
                    <h4>{{tr('notes')}}</h4>
                    <p>
                        </p><ul>
                            <li>{{tr('free_users_notes')}}</li>
                        </ul>
                    <p></p>
                </div>
            </div>
            
            @else
            <div class="box-body">
                <div class="callout bg-pale-secondary">
                    <h4>{{tr('notes')}}</h4>
                    <p>
                        </p><ul>
                            <li>{{tr('users_notes1')}}</li>
                            <li>{{tr('users_notes2')}}</li>
                            <li>{{tr('badge_note')}}</li>
                        </ul>
                    <p></p>
                </div>
            </div>
            @endif
            <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        @include('admin.users._search')

                        <table id="checkBoxData" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    
                                    <th>
                                    @if($users->count() >= 1)

                                        <div class="checkbox">
                                            <input type="checkbox" id="basic_checkbox" class="check_all">
                                            <label for="basic_checkbox"></label>                  
                                        </div>
                                    @endif
                                    </th>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('email_mobile') }}</th>
                                    <th>{{ tr('is_content_creator') }}</th>
                                    {{-- <th>{{ tr('user_type') }}</th> --}}
                                    <th>{{ tr('wallet_balance') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    {{-- <th><i class="icon-envelope"></i> {{ tr('is_email_verified') }}</th> --}}
                                    {{-- <th>{{ tr('two_step_auth') }}</th> --}}
                                    @if(Setting::get('is_verified_badge_enabled'))
                                    <th>{{tr('is_badge_verified')}}</th>
                                    @endif

                                    <th>{{tr('documents')}}</th>

                                    <th>&nbsp;&nbsp;{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($users as $i => $user)

                                <tr>

                                    <td id="check{{$user->id}}">
                                        <input type="checkbox" name="row_check" class="faChkRnd chk-box-inner-left" id="basic_checkbox_{{$user->id}}" value="{{$user->id}}">
                                        <label for="basic_checkbox_{{$user->id}}"></label>
                                    </td>

                                    <td>{{ $i+$users->firstItem() }}</td>

                                    <td class="white-space-nowrap">
                                        <a href="{{route('admin.users.view' , ['user_id' => $user->id])}}" class="custom-a">
                                            {{$user->name ?: tr('not_available')}}
                                        </a>

                                        @if($user->is_verified_badge == YES && Setting::get('is_verified_badge_enabled'))
                                        <img src="{{Setting::get('verified_badge_file')}}" width="16" height="16" />
                                        @endif

                                        @if($user->user_account_type == USER_PREMIUM_ACCOUNT)
                                        <b><i class="icon-badge text-green"></i></b>
                                        @endif

                                        @if(Setting::get('is_user_active_status') == YES)

                                        @if(Cache::has($user->id))
                                        <i class="fa fa-circle text-green" aria-hidden="true" title="Active"></i>
                                        @else
                                        <i class="fa fa-circle-thin" aria-hidden="true" title="Away"></i>
                                        @endif

                                        @endif

                                    </td>

                                    <td>{{ $user->email }}<br>
                                        <span class="custom-muted">
                                            {{$user->mobile ?: ""}}
                                        </span>

                                        <p class="custom-muted">
                                            @if($user->user_account_type == USER_PREMIUM_ACCOUNT)
                                            
                                                <span class="badge badge-success">{{ tr('USER_PREMIUM_ACCOUNT') }}</span>

                                            @else

                                                <span class="badge badge-warning">{{ tr('USER_FREE_ACCOUNT') }}</span>

                                            @endif

                                            <span class="custom-muted">
                                                @if($user->is_email_verified == USER_EMAIL_NOT_VERIFIED)

                                                    <a href="{{ route('admin.users.verify', ['user_id' => $user->id]) }}" title="Click Here to Verify Email">
                                                        <img src="{{asset('admin-assets/assets/images/email-unverified.svg')}}" />
                                                    </a>

                                                @else

                                                    <img src="{{asset('admin-assets/assets/images/email-verified.svg')}}" title="Email Address Verified"/>

                                                @endif
                                            </span>

                                        </p>
                                    </td>

                                    <td>
                                        @if($user->is_content_creator == CONTENT_CREATOR)
                                            
                                        <span class="badge badge-success">{{ tr('yes') }}</span>

                                        @else

                                        <span class="badge badge-primary">{{ tr('no') }}</span>

                                        @endif

                                    </td>

                                    <td>
                                        <a href="{{route('admin.user_wallets.view' , ['user_id' => $user->id])}}">
                                            {{$user->userWallets->remaining_formatted ?? formatted_amount(0.00)}}
                                        </a>
                                    </td>

                                    <td>
                                        @if($user->status == USER_APPROVED)

                                        <span class="badge badge-success">{{ tr('approved') }}</span>

                                        @else

                                        <span class="badge badge-warning">{{ tr('declined') }}</span>

                                        @endif
                                    </td>

                                    {{-- <td>
                                        @if($user->is_email_verified == USER_EMAIL_NOT_VERIFIED)

                                        <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.users.verify', ['user_id' => $user->id]) }}">
                                            <i class="icon-close"></i> {{ tr('verify') }}
                                        </a>

                                        @else

                                        <span class="badge badge-success">{{ tr('verified') }}</span>

                                        @endif
                                    </td> --}}

                                    {{-- <td>
                                        <center>
                                        @if($user->is_two_step_auth_enabled == TWO_STEP_AUTH_DISABLE)

                                        <span class="badge badge-warning">{{ tr('off') }}</span>

                                        @else

                                        <span class="badge badge-success">{{ tr('on') }}</span>

                                        @endif
                                        </center>
                                    </td> --}}

                                    @if(Setting::get('is_verified_badge_enabled'))

                                    <td>
                                        <center>
                                        @if($user->is_verified_badge == YES)

                                        <span class="badge badge-success">{{tr('yes')}}</span>

                                        @else
                                        <span class="badge badge-warning">{{tr('no')}}</span>

                                        @endif
                                        </center>
                                    </td>

                                    @endif

                                    <td>
                                        <a class="btn btn-blue btn-sm" href="{{route('admin.user_documents.view', ['user_id' => $user->id])}}">{{$user->is_document_verified_formatted}}</a>
                                    </td>

                                    <td>

                                        @include('admin.users._action')

                                        <div class="btn-group" role="group" style="display:none">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <!-- <button type="button" class="btn btn-info btn-flat">{{ tr(' ') }}</button>
                                            <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button> -->

                                            <div class="dropdown-menu dropdown-lg-scroll" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.users.view', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('view') }}</a>

                                                @if($user->is_content_creator == CONTENT_CREATOR)

                                                <a class="dropdown-item" href="{{ route('admin.users.report_dashboard', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('report_dashboard') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.users.dashboard', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('dashboard') }}</a>

                                                @else

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('upgrade_to_content_creator_confirmation' , $user->name) }}&quot;);" href="{{ route('admin.users.content_creator_upgrade', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('upgrade_to_content_creator') }}</a>

                                                @endif

                                                <a class="dropdown-item" href="{{ route('admin.users.view', ['user_id' => $user->id] ) }}" data-toggle="modal" data-target="#{{$user->id}}">&nbsp;{{ ($user->user_account_type  == USER_FREE_ACCOUNT) ? tr('upgrade_to_premium') : tr('update_premium') }}</a>

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.edit', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('user_delete_confirmation' , $user->name) }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($user->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.users.status' , ['user_id' => $user->id] )  }}" onclick="return confirm(&quot;{{ $user->name }} - {{ tr('user_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.status' , ['user_id' => $user->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                @if(Setting::get('is_verified_badge_enabled'))


                                                @if($user->is_verified_badge == YES)

                                                <a class="dropdown-item" href="{{  route('admin.users.verify_badge' , ['user_id' => $user->id] )  }}">&nbsp;{{ tr('remove_badge') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.verify_badge' , ['user_id' => $user->id] ) }}">&nbsp;{{ tr('add_badge') }}</a>

                                                @endif

                                                @endif

                                                @if($user->is_content_creator == CONTENT_CREATOR)
                                                <a class="dropdown-item" href="{{ route('admin.posts.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('posts') }}</a>
                                                @endif

                                                <a class="dropdown-item" href="{{route('admin.user_followings',['following_id' => $user->id])}}">&nbsp;{{ tr('followings') }}</a>

                                                <a class="dropdown-item" href="{{route('admin.user_followers', ['user_id' => $user->id])}}">&nbsp;{{ tr('followers') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.orders.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('orders') }}</a>

                                                @if($user->user_account_type == USER_PREMIUM_ACCOUNT)
                                                <a class="dropdown-item" href="{{ route('admin.post.payments', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('post_payments') }}</a>
                                                @endif
                                                 
                                                <a class="dropdown-item" href="{{route('admin.live_videos.payments',['user_id' => $user->id])}}">&nbsp;{{tr('live_video_payments')}}</a>
                                               
                                                <a class="dropdown-item" href="{{route('admin.video_call_payments.index',['user_id' => $user->id])}}">&nbsp;{{tr('video_call_payments')}}</a>

                                                <a class="dropdown-item" href="{{route('admin.audio_call_payments.index',['user_id' => $user->id])}}">&nbsp;{{tr('audio_call_payments')}}</a>
                                                
                                                <a class="dropdown-item" href="{{route('admin.users_subscriptions.index',['from_user_id' => $user->id])}}">&nbsp;{{tr('subscription_payments')}}</a>


                                                <a class="dropdown-item" href="{{ route('admin.delivery_address.index', ['user_id' => $user->id] ) }}" style="display: none;">&nbsp;{{ tr('delivery_address') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.bookmarks.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('bookmarks') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.fav_users.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('favorite_users') }}</a>


                                                <a class="dropdown-item" href="{{route('admin.post_likes.index', ['user_id' => $user->id])}}">&nbsp;{{ tr('liked_posts') }}</a>

                                                <a class="dropdown-item" href="{{route('admin.user_documents.view', array('user_id'=>$user->id))}}">&nbsp;{{tr('documents')}}</a>

                                                <a class="dropdown-item" href="{{ route('admin.user_wallets.view', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('wallet') }}</a>

                                                <a class="dropdown-item" href="{{route('admin.user_withdrawals', array('user_id'=>$user->id))}}">&nbsp;{{ tr('withdrawal')}}</a>

                                                <a class="dropdown-item" href="{{ route('admin.user_tips.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('tip_payments') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.users.billing_accounts', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('billing_accounts') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.users.carts', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('cart') }}</a>

                                                @if($user->is_content_creator == CONTENT_CREATOR)
                                                <!-- <a class="dropdown-item" href="{{ route('admin.promo_codes.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('promo_codes') }}</a> -->

                                                <a class="dropdown-item" href="{{ route('admin.user_products.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('products') }}</a>
                                                @endif

                                                <a class="dropdown-item" href="{{route('admin.block_users.index', ['user_id'=>$user->id] )}}"> &nbsp;{{tr('blocked_users')}}</a>

                                                <a class="dropdown-item" href="{{route('admin.stories.index', ['user_id'=>$user->id] )}}"> &nbsp;{{tr('story')}}</a>

                                                <a class="dropdown-item" href="{{route('admin.user_login_session.index', ['user_id'=>$user->id] )}}"> &nbsp;{{tr('user_sessions')}}</a>
                                            </div>

                                        </div>

                                    </td>

                                </tr>
                                <!-- modal start -->

                                @include('admin.users._premium_account_form')

                                @include('admin.users._custom_report_form')

                                <!-- Modal -->

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $users->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /card -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->

@endsection

@section('scripts')

@if(Session::has('bulk_action'))
<script type="text/javascript">
    $(document).ready(function() {
        localStorage.clear();
    });
</script>
@endif

<script type="text/javascript">
    $(document).ready(function() {
        get_values();

        // Call to Action for Delete || Approve || Decline
        $('.action_list').click(function() {
            var selected_action = $(this).attr('id');
            if (selected_action != undefined) {
                $('#action').val(selected_action);
                if ($("#selected_ids").val() != "") {
                    if (selected_action == 'bulk_delete') {
                        var message = "{{ tr('admin_users_delete_confirmation') }}";
                    } else if (selected_action == 'bulk_approve') {
                        var message = "{{ tr('admin_users_approve_confirmation') }}";
                    } else if (selected_action == 'bulk_decline') {
                        var message = "{{ tr('admin_users_decline_confirmation') }}";
                    }
                    var confirm_action = confirm(message);

                    if (confirm_action == true) {
                        $("#users_form").submit();
                    }
                    // 
                } else {
                    alert('Please select the check box');
                }
            }
        });
        // single check
        var page = $('#page_id').val();
        $('.faChkRnd:checkbox[name=row_check]').on('change', function() {

            var checked_ids = $(':checkbox[name=row_check]:checked').map(function() {
                    return $(this).val()
                })
                .get();
           
            localStorage.setItem("user_checked_items" + page, JSON.stringify(checked_ids));

            get_values();

        });
        // select all checkbox
        $(".check_all").on("click", function() {
            if ($("input:checkbox").prop("checked")) {
                $("input:checkbox[name='row_check']").prop("checked", true);
                var checked_ids = $(':checkbox[name=row_check]:checked').map(function() {
                        return $(this).val()
                    })
                    .get();
                // var page = {!! $users->lastPage() !!};
                console.log("user_checked_items" + page);

                localStorage.setItem("user_checked_items" + page, JSON.stringify(checked_ids));
                get_values();
            } else {
                $("input:checkbox[name='row_check']").prop("checked", false);
                localStorage.removeItem("user_checked_items" + page);
                get_values();
            }

        });

        // Get Id values for selected User
        function get_values() {
            var pageKeys = Object.keys(localStorage).filter(key => key.indexOf('user_checked_items') === 0);
            var values = Array.prototype.concat.apply([], pageKeys.map(key => JSON.parse(localStorage[key])));

            if (values) {
                $('#selected_ids').val(values);
            }

            for (var i = 0; i < values.length; i++) {
                $('#' + values[i]).prop("checked", true);
            }
        }



    });

    // to accept trailing zeroes
    $(document).ready(function() {
        // $('.non_zero').on('input change', function (e) {
        //     var reg = /^0+/gi;
        //     if (this.value.match(reg)) {
        //         this.value = this.value.replace(reg, '');
        //     }
        // });
    });

    $(document).ready(function(e) {

        $(".card-dashboard").scroll(function() {
            if ($('.chk-box-inner-left').length <= 5) {
                $(this).removeClass('table-responsive');
            }
        });

    });
</script>

@endsection