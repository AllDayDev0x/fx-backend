@extends('layouts.admin') 

@section('title', tr('liked_posts')) 

@section('content-header', tr('liked_posts')) 

@section('breadcrumb')

<li class="breadcrumb-item active"><a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>
    
<li class="breadcrumb-item">{{ tr('liked_posts') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('liked_posts') }} - <a href="{{route('admin.users.view',['user_id'=>$user->id ?? '#'])}}">{{$user->name ?? ''}}</a></h4>                    
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        @include('admin.post_likes._search')
                        
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('content_creator_name') }}</th>
                                    <th>{{ tr('postname') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($post_likes as $i => $post_like)

                                <tr>
                                    <td>{{ $i + $post_likes->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $post_like->post_user_id] )  }}">
                                        {{ $post_like->username ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.posts.view' , ['post_id' => $post_like->post_id] )  }}">
                                        {!! $post_like->post->content ?? "-" !!}
                                        </a>
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-success dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                
                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                        
                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('post_delete_confirmation') }}&quot;);" href="{{ route('admin.post_likes.delete', ['post_like_id' => $post_like->id,'user_id' => $post_like->user_id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                            </div>

                                        </div>


                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $post_likes->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection