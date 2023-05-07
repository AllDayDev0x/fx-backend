@extends('layouts.admin')

@section('title', tr('categories'))

@section('content-header', tr('categories'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.categories.index')}}">{{ tr('categories') }}</a>
</li>

<li class="breadcrumb-item">{{$title ?? tr('view_category')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{$title ?? tr('view_category')}}</h4>

                    <div class="heading-elements">

                       @if($categories->count() >= 1)
                        <a class="btn btn-primary  dropdown-toggle  bulk-action-dropdown" href="#" id="dropdownMenuOutlineButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-plus"></i> {{tr('bulk_action')}}
                        </a>
                       @endif

                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_category') }}</a>

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

                                  <form action="{{route('admin.categories.bulk_action')}}" id="user_category_form" method="POST" role="search">

                                    @csrf

                                    <input type="hidden" name="action_name" id="action" value="">

                                    <input type="hidden" name="selected_categories" id="selected_ids" value="">

                                    <input type="hidden" name="page_id" id="page_id" value="{{ (request()->page) ? request()->page : '1' }}">

                                   </form>

                                </div>
                    </div>

                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    @include('admin.categories._search')

                    <div class="table-responsive">

                        <table id="checkBoxData" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    @if($categories->count() >= 1)
                                    <th>
                                         <div class="checkbox">
                                            <input type="checkbox" id="basic_checkbox" class="check_all">
                                            <label for="basic_checkbox"></label>                  
                                        </div>
                                    </th>
                                    @endif
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('category_name') }}</th>
                                    <th>{{ tr('picture') }}</th>
                                    <th>{{ tr('total_users') }}</th>
                                    <th>{{ tr('description') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($categories as $i => $category)

                                <tr>

                                    <td id="check{{$category->id}}">
                                        <input type="checkbox" name="row_check" class="faChkRnd chk-box-inner-left" id="basic_checkbox_{{$category->id}}" value="{{$category->id}}">
                                        <label for="basic_checkbox_{{$category->id}}"></label>
                                    </td>


                                    <td>{{ $i+$categories->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.categories.view' , ['category_id' => $category->id])}}" class="custom-a">
                                            {{$category->name ?: tr('n_a')}}
                                        </a>

                                    </td>

                                    <td><img src="{{$category->picture ?: asset('categories-placeholder.png')}}" class="category-image"></td>

                                    <td>
                                        <a href="{{ route('admin.users.index', ['category_id' => $category->id] ) }}">
                                        {{ $category->total_users  ?: 0}}
                                        </a>
                                    </td>


                                    <td>
                                        {!! $category->description  ?: tr('n_a') !!}
                                    </td>

                                    <td>
                                        @if($category->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>

                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span>

                                        @endif
                                    </td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu dropdown-sm-scroll" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.categories.view', ['category_id' => $category->id] ) }}">&nbsp;{{ tr('view') }}</a>


                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.categories.edit', ['category_id' => $category->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('category_delete_confirmation' , $category->name) }}&quot;);" href="{{ route('admin.categories.delete', ['category_id' => $category->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($category->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.categories.status' , ['category_id' => $category->id] )  }}" onclick="return confirm(&quot;{{ $category->name }} - {{ tr('category_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.categories.status' , ['category_id' => $category->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                <a class="dropdown-item" href="{{ route('admin.users.index', ['category_id' => $category->id] ) }}">&nbsp;{{ tr('total_users') }}</a>

                                            </div>

                                        </div>

                                    </td>

                                </tr>


                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $categories->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

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
                        var message = "{{ tr('admin_categories_delete_confirmation') }}";
                    } else if (selected_action == 'bulk_approve') {
                        var message = "{{ tr('category_approve_confirmation') }}";
                    } else if (selected_action == 'bulk_decline') {
                        var message = "{{ tr('category_decline_confirmation') }}";
                    }
                    var confirm_action = confirm(message);

                    if (confirm_action == true) {
                        $("#user_category_form").submit();
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

            localStorage.setItem("category_checked_items" + page, JSON.stringify(checked_ids));

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
                
                console.log("category_checked_items" + page);

                localStorage.setItem("category_checked_items" + page, JSON.stringify(checked_ids));
                get_values();
            } else {
                $("input:checkbox[name='row_check']").prop("checked", false);
                localStorage.removeItem("category_checked_items" + page);
                get_values();
            }

        });

        // Get Id values for selected User
        function get_values() {
            var pageKeys = Object.keys(localStorage).filter(key => key.indexOf('category_checked_items') === 0);
            var values = Array.prototype.concat.apply([], pageKeys.map(key => JSON.parse(localStorage[key])));

            if (values) {
                $('#selected_ids').val(values);
            }

            for (var i = 0; i < values.length; i++) {
                $('#' + values[i]).prop("checked", true);
            }
        }



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