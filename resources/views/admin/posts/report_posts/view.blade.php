@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('reported_users'))

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.posts.index')}}">{{ tr('posts') }}</a>
</li>

<li class="breadcrumb-item active">
    {{tr('view_report_users')}}
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{ tr('view_report_users') }} -

                        <a href="{{route('admin.posts.view',['post_id'=>$post->id ?? ''])}}">{{$post->post_unique_id ?? ''}}</a>

                    </h4>

                    <div class="heading-elements">

                        <a onclick="return confirm(&quot;{{ tr('post_delete_confirmation' , $post->post_unique_id ?? '') }}&quot;);" href="{{ route('admin.posts.delete', ['post_id' => $post->id,'page'=>request()->input('page')] ) }}" class="btn btn-primary"><i class="ft-trash icon-left"></i>{{tr('delete_post')}}</a>

                        @if($post->status == APPROVED)

                            <a class="btn btn-warning" href="{{  route('admin.posts.status' , ['post_id' => $post->id] )  }}" onclick="return confirm(&quot;{{ tr('post_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                            </a>

                        @else

                            <a class="btn btn-success" href="{{ route('admin.posts.status' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                        @endif
                   
                    </div>

                    

                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>

                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('post_id') }}</th>
                                    <th>{{ tr('reported_user') }}</th>
                                    <th>{{ tr('reason') }}</th>
                                    <th>&nbsp;&nbsp;&nbsp;{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($report_posts as $i => $report_post)
                                <tr>
                                    <td>{{ $i+$report_posts->firstItem() }}</td>

                                    <td>

                                        <a href="{{route('admin.posts.view',['post_id'=>$report_post->post->id ?? ''])}}">
                                            {{$report_post->post->post_unique_id ?? tr('n_a') }}
                                        </a>

                                    </td>

                                    <td>
                                        <a href="{{route('admin.users.view',['user_id'=>$report_post->block_by ?? ''])}}">
                                            {{$report_post->blockeduser->name ?? tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>{{$report_post->reason ?: tr('n_a') }}</td>


                                    <td>

                                        <a class="btn btn-outline-warning btn-large" onclick="return confirm(&quot; {{ tr('delete_confirmation') }}&quot;);" href="{{route('admin.report_posts.delete',['report_post_id'=>$report_post->id])}}">&nbsp;{{ tr('delete_report') }}</a>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $report_posts->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection