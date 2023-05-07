@extends('layouts.admin')

@section('title', tr('stories'))

@section('content-header', tr('stories'))

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.posts.index')}}">{{ tr('stories') }}</a>
</li>

<li class="breadcrumb-item active">
    {{ tr('view_stories')}}
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card view-post-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{ tr('view_stories') }}

                        @if($user)
                        - 
                        <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? tr('n_a')}}</a>

                        @endif
                    </h4>

                </div>

                <div class="box box-outline-purple">
                   <div class="box-body">

                        @include('admin.stories._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('story_id') }}</th>
                                    <th>{{ tr('publish_time') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($stories as $i => $story)
                                <tr>
                                    <td>{{ $i+$stories->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $story->user_id] )  }}">
                                            {{ $story->user_displayname ?: tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>
                                      <a href="{{  route('admin.stories.view' , ['story_id' => $story->id] )  }}">
                                        {{ $story->unique_id ?: tr('n_a')}}
                                      </a>
                                    </td>

                                    <td>{{($story->publish_time) ? common_date($story->publish_time , Auth::guard('admin')->user()->timezone) : '-'}}</td>
                                    
                                    <td>
                                        @if($story->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>


                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.stories.view', ['story_id' => $story->id] ) }}">&nbsp;{{ tr('view') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.stories.edit', ['story_id' => $story->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                @if(Setting::get('is_demo_control_enabled') == YES)



                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('story_delete_confirmation' , $story->unique_id) }}&quot;);" href="{{ route('admin.stories.delete', ['story_id' => $story->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($story->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.stories.status' , ['story_id' => $story->id] )  }}" onclick="return confirm(&quot;{{ tr('story_decline_confirmation
') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.stories.status' , ['story_id' => $story->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $stories->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
