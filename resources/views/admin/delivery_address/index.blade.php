@extends('layouts.admin') 

@section('title', tr('delivery_address')) 

@section('content-header', tr('delivery_address')) 

@section('breadcrumb')


    
<li class="breadcrumb-item">{{ tr('delivery_address') }}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('delivery_address') }}

                    @if($user)
                    -
                    <a href="{{route('admin.users.view',['user_id'=>$user->id])}}">{{$user->name ?? ''}}</a>
                 
                    @endif

                    </h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.delivery_address._search')
                        
                        <table class="table table-striped table-bordered sourced-data table-responsive">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('pincode') }}</th>
                                    <th>{{ tr('state')}}</th>
                                    <th>{{ tr('landmark')}}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($delivery_addresses as $i => $delivery_address_details)

                                <tr>
                                    <td>{{ $i + $delivery_addresses->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $delivery_address_details->user_id] )  }}">
                                        {{ $delivery_address_details->user->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $delivery_address_details->name ?? "-" }}</td>

                                    <td>
                                        {{ $delivery_address_details->pincode }}
                                    </td>

                                    <td>{{ $delivery_address_details->state }}</td>

                                    <td>{{ $delivery_address_details->landmark }}
                                    </td>

                                    <td>
                                        @if($delivery_address_details->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.delivery_address.view', ['delivery_address_id' => $delivery_address_details->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                        
                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('delivery_address_delete_confirmation' , $delivery_address_details->name) }}&quot;);" href="{{ route('admin.delivery_address.delete', ['delivery_address_id' => $delivery_address_details->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                            </div>

                                        </div>


                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $delivery_addresses->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection