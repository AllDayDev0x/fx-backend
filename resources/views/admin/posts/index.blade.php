@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.posts.index')}}">{{ tr('posts') }}</a>
</li>

<li class="breadcrumb-item active">
    {{Request::get('scheduled') ? tr('scheduled_posts') : tr('view_posts')}}
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card view-post-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        @if(Request::get('scheduled'))

                        {{tr('scheduled_posts')}}

                        @else

                        {{ tr('view_posts') }}

                        @endif

                        @if($user)
                        - 
                        <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? tr('n_a')}}</a>

                        @endif

                        @if($category)
                        -
                        <a href="{{route('admin.categories.view',['category_id'=>$category->id ?? ''])}}">{{$category->name ?: tr('n_a')}}</a>

                        @endif

                        @if($hashtag)
                        -
                        <a href="{{route('admin.hashtags.view',['hashtag_id'=>$hashtag->id ?? ''])}}">#{{$hashtag->name ?: tr('n_a')}}</a>

                        @endif

                    </h4>

                    <div class="heading-elements">

                        @if($posts->count() > 1)
                        <a class="btn btn-primary  dropdown-toggle  bulk-action-dropdown" href="#" id="dropdownMenuOutlineButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-plus"></i> {{tr('bulk_action')}}
                        </a>
                        @endif

                        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_post') }}</a>

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

                        <div class="bulk_action">

                            <form action="{{route('admin.posts.bulk_action')}}" id="posts_form" method="POST" role="search">

                                @csrf

                                <input type="hidden" name="action_name" id="action" value="">

                                <input type="hidden" name="selected_posts" id="selected_ids" value="">

                                <input type="hidden" name="page_id" id="page_id" value="{{ (request()->page) ? request()->page : '1' }}">

                            </form>

                        </div>


                    </div>

                </div>

                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('view_posts_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">
                   <div class="box-body">

                        @include('admin.posts._search')
                        <div class="table-responsive">
                        <table id="checkBoxData" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>
                                        @if($posts->count() > 1)
                                        <div class="checkbox">
                                            <input type="checkbox" id="basic_checkbox" class="check_all">
                                            <label for="basic_checkbox"></label>                  
                                        </div>
                                        @endif
                                    </th>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('post_id') }}</th>
                                    <th>{{ tr('publish_time') }}</th>
                                    <th>{{ tr('is_paid_post') }}</th>
                                    <th>{{ tr('amount') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($posts as $i => $post)
                                <tr>
                                    <td id="check{{$post->id}}">
                                        <input type="checkbox" name="row_check" class="faChkRnd chk-box-inner-left" id="basic_checkbox_{{$post->id}}" value="{{$post->id}}">
                                        <label for="basic_checkbox_{{$post->id}}"></label>
                                    </td>


                                    <td>{{ $i+$posts->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $post->user_id] )  }}">
                                            {{ $post->userdisplayname ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                      <a href="{{  route('admin.posts.view' , ['post_id' => $post->id] )  }}">
                                        {{ $post->unique_id}}
                                      </a>
                                    </td>

                                    <td>{{($post->publish_time) ? common_date($post->publish_time , Auth::guard('admin')->user()->timezone) : '-'}}</td>

                                    <td>
                                        @if($post->is_paid_post)
                                        <span class="badge badge-success">{{tr('yes')}}</span>
                                        @else
                                        <span class="badge badge-danger">{{tr('no')}}</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $post->amount_formatted}}
                                    </td>

                                    <td>
                                        @if($post->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>


                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu dropdown-sm-scroll" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.posts.dashboard', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('dashboard') }}</a>


                                                <a class="dropdown-item" href="{{ route('admin.posts.view', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('view') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.posts.edit', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                @if(Setting::get('is_demo_control_enabled') == YES)



                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('post_delete_confirmation' , $post->unique_id) }}&quot;);" href="{{ route('admin.posts.delete', ['post_id' => $post->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($post->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.posts.status' , ['post_id' => $post->id] )  }}" onclick="return confirm(&quot;{{ tr('post_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.posts.status' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                <a class="dropdown-item" href="{{ route('admin.posts.comments' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('comments') }}</a>
                                                @if($post->is_published == NO)

                                                <a class="dropdown-item" href="{{ route('admin.posts.publish' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('publish') }}</a>

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $posts->appends(request()->input())->links('pagination::bootstrap-4') }}</div>
                      </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

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
                        var message = "{{ tr('admin_posts_delete_confirmation') }}";
                    } else if (selected_action == 'bulk_approve') {
                        var message = "{{ tr('admin_posts_approve_confirmation') }}";
                    } else if (selected_action == 'bulk_decline') {
                        var message = "{{ tr('admin_posts_decline_confirmation') }}";
                    }
                    var confirm_action = confirm(message);

                    if (confirm_action == true) {
                        $("#posts_form").submit();
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

            localStorage.setItem("posts_checked_items" + page, JSON.stringify(checked_ids));

            get_values();

        });
        // select all checkbox
        $(".check_all").on("click", function() {
            if ($("input:checkbox").prop("checked")) {
                $("input:checkbox[name='row_check']").prop("checked", true);
                var checked_ids = $(':checkbox[name=row_check]:checked').map(function() {
                        return $(this).val();
                    })
                    .get();
                console.log("posts_checked_items" + page);

                localStorage.setItem("posts_checked_items" + page, JSON.stringify(checked_ids));
                get_values();
            } else {
                $("input:checkbox[name='row_check']").prop("checked", false);
                localStorage.removeItem("posts_checked_items" + page);
                get_values();
            }

        });

        // Get Id values for selected User
        function get_values() {
            var pageKeys = Object.keys(localStorage).filter(key => key.indexOf('posts_checked_items') === 0);
            var values = Array.prototype.concat.apply([], pageKeys.map(key => JSON.parse(localStorage[key])));

            if (values) {
                $('#selected_ids').val(values);
            }

            for (var i = 0; i < values.length; i++) {
                $('#' + values[i]).prop("checked", true);
            }
        }



    });
</script>

@endsection