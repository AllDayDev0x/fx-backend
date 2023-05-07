@extends('layouts.admin')

@section('content-header', tr('hashtags'))

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.hashtags.index' )}}">{{tr('hashtags')}}</a></li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('view_hashtags') }}</span>
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">
                <div class="card-header border-bottom border-gray">
                    
                        <h4 class="card-title">{{ tr('hashtags') }}</h4>

                        <div class="heading-elements">
                            <a href="{{ route('admin.hashtags.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_hashtag') }}</a>
                        </div>
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">
                        @include('admin.hashtags._search')
                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                <thead>
                                    <tr>

                                        <th>{{ tr('s_no') }}</th>
                                        <th>{{ tr('hashtag_id') }}</th>
                                        <th>{{ tr('hashtag_name') }}</th>
                                        <th>{{ tr('total_posts') }}</th>
                                        <th>{{ tr('status') }}</th>
                                        <th>{{ tr('action') }}</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($hashtags as $i => $hashtag)
                                    <tr>    
                                        
                                        <td>{{ $i+$hashtags->firstItem() }}</td>

                                        <td>
                                            <a href="{{ route('admin.hashtags.view', ['hashtag_id' => $hashtag->id] ) }}">
                                                {{ $hashtag->unique_id ?: tr('n_a') }}
                                            </a>
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.hashtags.view', ['hashtag_id' => $hashtag->id] ) }}">
                                                #{{ $hashtag->name ?: tr('n_a')}}
                                            </a>
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.posts.index', ['hashtag_id' => $hashtag->id] ) }}">
                                             {{$hashtag->postHashtag->count() ?? 0}}
                                            </a>
                                        </td>

                                        <td>
                                            @if($hashtag->status == APPROVED)

                                                <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>
                                            @else

                                                <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span>
                                            @endif
                                        </td>


                                        <td>

                                            <div class="dropdown">
                                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuOutlineButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    {{tr('action')}}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">

                                                    <a class="dropdown-item" href="{{ route('admin.hashtags.view', ['hashtag_id' => $hashtag->id] ) }}">&nbsp;{{ tr('view') }}</a>


                                                    @if(Setting::get('is_demo_control_enabled') == YES)



                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                    @else

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('hashtag_delete_confirmation' , $hashtag->name) }}&quot;);" href="{{ route('admin.hashtags.delete', ['hashtag_id' => $hashtag->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                    @endif

                                                    <div class="dropdown-divider"></div>


                                                    @if($hashtag->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.hashtags.status' , ['hashtag_id' => $hashtag->id] )  }}" onclick="return confirm(&quot;{{ tr('hashtag_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                    </a>

                                                    @else

                                                    <a class="dropdown-item" href="{{ route('admin.hashtags.status' , ['hashtag_id' => $hashtag->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                    @endif

                                                    <a class="dropdown-item" href="{{ route('admin.posts.index', ['hashtag_id' => $hashtag->id] ) }}">&nbsp;{{ tr('total_posts') }}</a>

                                                </div>

                                            </div>

                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>

                            </table>

                            <div class="pull-right" id="paglink">{{ $hashtags->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                        </div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection