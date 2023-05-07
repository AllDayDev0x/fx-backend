@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.posts.index')}}">{{ tr('posts') }}</a>
</li>

<li class="breadcrumb-item active">
    {{tr('view_report_posts')}}
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card report-post-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{ tr('view_report_posts') }}

                    </h4>

                    <div class="heading-elements">


                    </div>

                </div>
                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('report_posts_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                    <div class="box-body">

                        <div class="table-responsive">

                            <form method="GET" action="{{route('admin.report_posts.index')}}">

                                <div class="row">

                                    <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

                                        <div class="input-group">
                                           
                                            <input type="text" class="form-control" name="search_key"
                                            placeholder="{{tr('search_by_post_id')}}" value="{{Request::get('search_key')}}"> <span class="input-group-btn">
                                            &nbsp

                                            <button type="submit" class="btn btn-default">
                                               <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                                            </button>

                                            <a class="btn btn-default reset-btn" href="{{route('admin.report_posts.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                                            </a>
                                               
                                            </span>

                                        </div>
                                        
                                    </div>

                                </div>

                            </form>

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>

                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('post_id') }}</th>
                                    <th>{{ tr('report_user_count') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($report_posts as $i => $post)
                                <tr>
                                    <td>{{ $i+$report_posts->firstItem() }}</td>

                                    <td>

                                        <a href="{{route('admin.posts.view',['post_id'=>$post->post->id ?? ''])}}">
                                            {{$post->post->post_unique_id ?? tr('n_a') }}
                                        </a>

                                    </td>

                                    <td>
                                        <a class="custom-a" href="{{route('admin.report_posts.view',['post_id'=>$post->post_id])}}">
                                            {{$post->report_user_count }}
                                        </a>
                                    </td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{route('admin.report_posts.view',['post_id'=>$post->post_id])}}">&nbsp;{{ tr('view') }}</a>

                                                @if($post->post->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.posts.status' , ['post_id' => $post->post_id] )  }}" onclick="return confirm(&quot;{{ tr('post_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.posts.status' , ['post_id' => $post->post_id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                <!-- <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('post_delete_confirmation' , $post->unique_id) }}&quot;);" href="{{ route('admin.posts.delete', ['post_id' => $post->post_id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a> -->

                                                <a class="dropdown-item" onclick="return confirm(&quot; {{ tr('delete_confirmation') }}&quot;);" href="{{route('admin.report_posts.delete',['post_id'=>$post->post_id])}}">&nbsp;{{ tr('delete_reports') }}</a>

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete_post') }}</a>

                                                @else

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('post_delete_confirmation' , $post->unique_id) }}&quot;);" href="{{ route('admin.posts.delete', ['post_id' => $post->post_id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete_post') }}</a>

                                                @endif

                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $report_posts->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection