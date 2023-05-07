@extends('layouts.admin')

@section('title', tr('vods'))

@section('content-header', tr('vods'))

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.posts.index')}}">{{ tr('vods') }}</a>
</li>

<li class="breadcrumb-item active">
    {{ tr('view_vods')}}
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card view-post-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{ tr('view_vods') }}

                        @if($user)
                        - 
                        <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? tr('n_a')}}</a>

                        @endif
                    </h4>

                </div>

                <div class="box box-outline-purple">
                   <div class="box-body">

                        @include('admin.vod_videos._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('unique_id') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($vod_videos as $i => $vod_video)
                                <tr>
                                    <td>{{ $i+$vod_videos->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $vod_video->user_id] )  }}">
                                            {{ $vod_video->user_displayname ?: tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>
                                      <a href="{{  route('admin.vod_videos.view' , ['vod_id' => $vod_video->id] )  }}">
                                        {{ $vod_video->unique_id ?: tr('n_a')}}
                                      </a>
                                    </td>

                                    <td>
                                        @if($vod_video->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>


                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.vod_videos.view', ['vod_id' => $vod_video->id] ) }}">&nbsp;{{ tr('view') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.vod_videos.edit', ['vod_id' => $vod_video->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                @if(Setting::get('is_demo_control_enabled') == YES)



                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('vod_delete_confirmation' , $vod_video->unique_id) }}&quot;);" href="{{ route('admin.vod_videos.delete', ['vod_id' => $vod_video->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($vod_video->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.vod_videos.status' , ['vod_id' => $vod_video->id] )  }}" onclick="return confirm(&quot;{{ tr('vod_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.vod_videos.status' , ['vod_id' => $vod_video->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $vod_videos->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
