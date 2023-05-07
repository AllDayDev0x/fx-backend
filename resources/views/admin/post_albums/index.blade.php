@extends('layouts.admin') 

@section('title', tr('post_albums')) 

@section('content-header', tr('post_albums')) 

@section('breadcrumb')


    
<li class="breadcrumb-item active">
    <a href="">{{ tr('post_albums') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_post_albums') }}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_post_albums') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">


                        <table class="table table-striped table-bordered sourced-data table-responsive">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('unique_id') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('content_creator') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($post_albums as $i => $post_album)
                                <tr>
                                    <td>{{ $i+$post_albums->firstItem() }}</td>

                                    <td>{{$post_album->unique_id}}</td>

                                    <td>{{$post_album->name}}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $post_album->user_id] )  }}">
                                        {{ $post_album->user->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        @if($post_album->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>


                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.post_albums.view', ['post_album_id' => $post_album->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                  

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('post_album_delete_confirmation' , $post_album->name) }}&quot;);" href="{{ route('admin.post_albums.delete', ['post_album_id' => $post_album->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($post_album->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.post_albums.status' , ['post_album_id' => $post_album->id] )  }}" onclick="return confirm(&quot;{{ tr('post_album_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.post_albums.status' , ['post_album_id' => $post_album->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $post_albums->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection