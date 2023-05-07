@extends('layouts.admin')

@section('title', tr('comments_list'))

@section('content-header', tr('comments_list'))

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.posts.index')}}">{{ tr('posts') }}</a>
</li>

<li class="breadcrumb-item active">
    {{tr('comments_list')}}
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{ tr('comments_list') }}
                            @if($post)
                            <a href="{{  route('admin.posts.view' , ['post_id' => $post->id??''] )  }}">
                                {{ $post->unique_id ?? tr('n_a')}}
                            </a>
                            @endif

                    </h4>

                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        <form method="GET" action="{{route('admin.posts.comments')}}">

                            <div class="row search-form-css">

                                <div class="col-xl-6 col-lg-6 col-md-12">
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-12">

                                    <div class="input-group">

                                        <input type="hidden" id="post_id" name="post_id" value="{{Request::get('post_id') ?? ''}}">

                                        <input type="text" class="form-control" name="search_key" placeholder="{{tr('comment_search_placeholder')}}" value="{{Request::get('search_key')}}"> <span class="input-group-btn">
                                            &nbsp

                                            <button type="submit" class="btn btn-default">
                                                <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                                            </button>

                                            <a class="btn btn-default reset-btn" href="{{route('admin.posts.comments',['post_id'=>$post->id??''])}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                                            </a>
                                            

                                        </span>

                                    </div>

                                </div>

                            </div>


                        </form>
                        <br>

                        <div class="table-responsive">

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('comments') }}</th>
                                    <th>&nbsp;&nbsp;{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($post_comments as $i => $post_comment)
                                <tr>
                                    <td>{{ $i+$post_comments->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $post_comment->user_id] )  }}">
                                            {{ $post_comment->username ?? "-" }}
                                        </a>
                                    </td>


                                    <td>
                                        {!! $post_comment->comment !!}
                                    </td>



                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">


                                                @if(Setting::get('is_demo_control_enabled') == YES)



                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('post_comment_delete_confirmation') }}&quot;);" href="{{ route('admin.post_comment.delete', ['comment_id' => $post_comment->id, 'post_id' => $post_comment->post_id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif


                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $post_comments->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div></div

        </div>

    </div>

</section>

@endsection