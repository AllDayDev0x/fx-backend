@extends('layouts.admin')

@section('title', tr('audio_call_requests'))

@section('content-header', tr('one_to_one_stream'))

@section('breadcrumb')

<li class="breadcrumb-item active">

    <a href="{{route('admin.audio_call_requests.index')}}">{{ tr('one_to_one_stream') }}</a>
</li>

<li class="breadcrumb-item">{{tr('audio_call_requests')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card user-view-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('audio_call_requests')}}</h4>

                </div>

                 <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('audio_call_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        @include('admin.audio_call_requests._search')

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

                                @foreach($audio_call_requests as $i => $audio_call_request)

                                <tr>
                                    

                                    <td>{{ $i+$audio_call_requests->firstItem() }}</td>

                                    <td>
                                       <a href="{{route('admin.audio_call_requests.view' , ['audio_call_request_id' => $audio_call_request->id])}}" class="custom-a">

                                        {{ $audio_call_request->unique_id ?: tr('n_a')}}
                                       </a>
                                    </td>

                                    <td class="white-space-nowrap">
                                        <a href="{{route('admin.users.view' , ['user_id' => $audio_call_request->user_id])}}" class="custom-a">
                                            {{$audio_call_request->user->name ?? tr('n_a')}}
                                        </a>
                                                                 
                                    </td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $audio_call_request->model_id])}}" class="custom-a">
                                            {{$audio_call_request->model->name ?? tr('not_available')}}
                                        </a>
                                    </td>

                                    <td>
                                        {{$audio_call_request->AudioCallPayments->paid_amount_formatted ?? formatted_amount(0)}}
                                    </td>

                                    <!-- <td>
                                        {{$audio_call_request->AudioCallPayments->payment_mode ?? tr('not_available')}}
                                    </td> -->

                                    <td>
                                        {{common_date($audio_call_request->start_time , Auth::guard('admin')->user()->timezone)}}
                                       
                                    </td>

                                    <td>
                                    <span class="btn btn-success btn-sm">
                                      {{call_status_formatted($audio_call_request->call_status,NO, $audio_call_request->payment_status)}}


                                    </span>
                                    </td>

                                    <td>

                                      @if($audio_call_request->audioCallPayments && $audio_call_request->audioCallPayments->status == PAID)
                                      <span class="btn btn-success btn-sm">
                                         {{tr('paid')}}
                                      </span>
                                      @else
                                       <span class="btn btn-danger btn-sm">
                                        {{tr('not_paid')}}
                                       </span>
                                      @endif
                                    </span>
                                    </td>

                                    <td>     
                                       <a class="btn btn-info" href="{{route('admin.audio_call_requests.view' , ['audio_call_request_id' => $audio_call_request->id])}}" style="color:#fff">
                                            {{tr('view')}}
                                        </a>

                                    </td>
                                   
                                </tr>
                            
                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $audio_call_requests->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

            </div>

        </div>

    </div>

</section>

@endsection

