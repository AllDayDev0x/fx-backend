@extends('layouts.admin')

@section('title', tr('video_calls'))

@section('content-header', tr('video_calls'))

@section('breadcrumb')

<li class="breadcrumb-item active">

    <a href="{{route('admin.video_call_requests.index')}}">{{ tr('video_calls') }}</a>
</li>

<li class="breadcrumb-item">{{tr('video_call_requests')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card user-view-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('video_call_requests')}}</h4>

                </div>

                 <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('video_call_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        @include('admin.video_call_requests._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                   
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('request_id') }}</th>
                                    <th>{{ tr('user_name') }}</th>
                                    <th>{{ tr('model') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <!-- <th>{{ tr('payment_mode') }}</th> -->
                                    <th>{{ tr('scheduled_date') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('payment_status') }}</th>
                                    <th>{{ tr('action') }}</th>

                                </tr>
                            </thead>

                            <tbody>

                                @foreach($video_call_requests as $i => $video_call_request)

                                <tr>
                                    

                                    <td>{{ $i+$video_call_requests->firstItem() }}</td>

                                    <td>
                                       <a href="{{route('admin.video_call_requests.view' , ['video_call_request_id' => $video_call_request->id])}}" class="custom-a">

                                        {{ $video_call_request->unique_id }}
                                       </a>
                                    </td>

                                    <td class="white-space-nowrap">
                                        <a href="{{route('admin.users.view' , ['user_id' => $video_call_request->user_id])}}" class="custom-a">
                                            {{$video_call_request->user->name ?? tr('not_available')}}
                                        </a>
                                                                 
                                    </td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $video_call_request->model_id])}}" class="custom-a">
                                            {{$video_call_request->model->name ?? tr('not_available')}}
                                        </a>
                                    </td>

                                    <td>
                                        {{$video_call_request->videoCallPayments->paid_amount_formatted ?? formatted_amount(0)}}
                                    </td>

                                    <!-- <td>
                                        {{$video_call_request->videoCallPayments->payment_mode ?? tr('not_available')}}
                                    </td> -->

                                    <td>
                                        {{common_date($video_call_request->start_time , Auth::guard('admin')->user()->timezone)}}
                                       
                                    </td>

                                    <td>
                                    <span class="btn btn-success btn-sm">
                                      {{call_status_formatted($video_call_request->call_status,NO, $video_call_request->payment_status)}}


                                    </span>
                                    </td>

                                    <td>
                                    
                                      @if($video_call_request->videoCallPayments && $video_call_request->videoCallPayments->status == PAID)
                                      <span class="btn btn-success btn-sm">
                                         {{tr('paid')}}
                                      </span>
                                      @else
                                       <span class="btn btn-danger btn-sm">
                                        {{tr('not_paid')}}
                                       </span>
                                      @endif

                                    </td>

                                    <td>     
                                       <a class="btn btn-info" href="{{route('admin.video_call_requests.view' , ['video_call_request_id' => $video_call_request->id])}}" style="color:#fff">
                                            {{tr('view')}}
                                        </a>

                                    </td>
                                   
                                </tr>
                            
                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $video_call_requests->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

            </div>

        </div>

    </div>

</section>

@endsection

