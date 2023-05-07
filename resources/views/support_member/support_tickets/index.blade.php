@extends('layouts.support_member') 

@section('content-header', tr('support_tickets')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('support_member.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item">
    <a href="{{route('support_member.support_tickets.index')}}">{{ tr('support_tickets') }}</a>
</li>

<li class="breadcrumb-item">{{ tr('view_support_tickets') }}
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_support_tickets') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('unique_id') }} </th> 
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('subject') }}</th>
                                    <th>{{ tr('message') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($support_tickets as $i => $support_ticket)
                                <tr>
                                    <td>{{ $i+$support_tickets->firstItem() }}</td>

                                    <td>{{ $support_ticket->unique_id}}</td>

                                    <td>
                                    
                                        {{ $support_ticket->user->name ?? "-" }}
                                    
                                    </td>

                                    <td>{{ substr($support_ticket->subject,0,10)}}...</td>

                                    <td>
                                        {{ substr($support_ticket->message,0,10)}}...
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('support_member.support_tickets.view', ['support_ticket_id' => $support_ticket->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                <div class="dropdown-divider"></div>

                                                <a class="dropdown-item" href="{{route('support_member.support_tickets.chat')}}">&nbsp;{{ tr('chat') }}</a> 

                                            </div>
                                            
                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $support_tickets->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection