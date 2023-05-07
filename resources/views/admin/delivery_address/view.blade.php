@extends('layouts.admin') 

@section('title', tr('delivery_address')) 

@section('content-header', tr('delivery_address')) 

@section('breadcrumb')


<li class="breadcrumb-item active">{{tr('delivery_address')}}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('delivery_address') }}
                        <!-- <a class="pull-right text-danger" onclick="return confirm(&quot;{{ tr('delivery_address_delete_confirmation' , $delivery_address_details->name) }}&quot;);" href="{{ route('admin.delivery_address.delete', ['delivery_address_id' => $delivery_address_details->id] ) }}"><i class="fa fa-trash"></i></a> -->
                    </h4>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">
                                    
                        <div class="row">

                            <div class="col-md-6">

                                <div class="list-group">

                                    <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0">{{$delivery_address_details->user->name ?? "-"}}</h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('delivery_address_user')}}</p>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0">{{$delivery_address_details->name}}</h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('name')}}</p>
                                            </div>

                                        </div>
                                        
                                    </div>

                                    <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0">{{$delivery_address_details->contact_number}}</h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('contact_number')}}</p>
                                            </div>

                                        </div>
                                        
                                    </div>

                                     <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0"> @if($delivery_address_details->status == APPROVED)

                                                    <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                                    <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                                </h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('status')}}</p>
                                            </div>

                                        </div>
                                        
                                    </div>

                                    <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0">{{$delivery_address_details->landmark}}</h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('landmark')}}</p>
                                            </div>

                                        </div>
                                        
                                    </div>
                                 
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="list-group">

                                    <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0">{{$delivery_address_details->address}}</h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('address')}}</p>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0">{{$delivery_address_details->state }}</h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('state')}}</p>
                                            </div>

                                        </div>
                                        
                                    </div>

                                    <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0">{{$delivery_address_details->pincode}}</h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('pincode')}}</p>
                                            </div>

                                        </div>
                                        
                                    </div>

                                    <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0">{{ common_date($delivery_address_details->updated_at,Auth::guard('admin')->user()->timezone) }}</h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('updated_at')}}</p>
                                            </div>

                                        </div>
                                        
                                    </div>

                                    <div class="list-group-item">

                                        <div class="media">
                                      
                                            <div class="media-body w-100">
                                                <h6 class="media-heading mb-0">{{ common_date($delivery_address_details->created_at,Auth::guard('admin')->user()->timezone) }}</h6>
                                                <p class="font-small-2 mb-0 text-muted">{{tr('created_at')}}</p>
                                            </div>

                                        </div>
                                        
                                    </div>
                                 
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
