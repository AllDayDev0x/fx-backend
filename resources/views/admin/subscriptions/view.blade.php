@extends('layouts.admin') 

@section('content-header', tr('subscriptions'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.subscriptions.index')}}">{{tr('subscriptions')}}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_subscriptions')}}</span>
    </li>
           
@endsection  

@section('content')

<section id="basic-form-layouts">
    
    <div class="row match-height">

		<div class="col-lg-12">

			<div class="card">

			    <div class="card-header border-bottom border-gray">

			       	<h4 class="text-uppercase">

			       		{{tr('view_subscriptions')}} 

			      	</h4>

			    </div>

			    <div class="card-content collapse show">
			    
				    <div class="card-body">

				      	<div class="row">

				        	<div class="col-6">
				        		<div class="card-title">{{tr('action')}}</div>

			        			<ul class="action-item">

			        				<li>
			        					@if($subscription->status == APPROVED)

							                <a class="btn btn-danger btn-min-width mr-1 mb-1" title="{{tr('decline')}}" href="{{ route('admin.subscriptions.status', ['subscription_id' => $subscription->id]) }}" onclick="return confirm(&quot;{{$subscription->title}} - {{tr('subscription_decline_confirmation')}}&quot;);" >
							                    <b>{{tr('decline')}}</b>
							                </a>

							            @else
							                
							                <a class="btn btn-success btn-min-width mr-1 mb-1" title="{{tr('approve')}}" href="{{ route('admin.subscriptions.status', ['subscription_id' => $subscription->id]) }}">
							                    <b>{{tr('approve')}}</b> 
							                </a>
							                   
							            @endif
				            		</li>

				            		<li>
			        					@if(Setting::get('admin_delete_control') == YES )

								      		<a href="{{ route('admin.subscriptions.edit', ['subscription_id' => $subscription->id] ) }}" class="btn btn-warning btn-min-width mr-1 mb-1" title="{{tr('edit')}}"><b>{{tr('edit')}}</b></a>

								      		<a onclick="return confirm(&quot;{{ tr('subscription_delete_confirmation', $subscription->title ) }}&quot;);" href="javascript:;" class="btn btn-danger btn-min-width mr-1 mb-1" title="{{tr('delete')}}"><b>{{tr('delete')}}</b>
								      			</a>

								   		@else
								   			<a href="{{ route('admin.subscriptions.edit' , ['subscription_id' => $subscription->id] ) }}" class="btn btn-warning btn-min-width mr-1 mb-1" title="{{tr('edit')}}"><b>{{tr('edit')}}</b></a>	
								      		                			
								      	 	<a onclick="return confirm(&quot;{{ tr('subscription_delete_confirmation', $subscription->title ) }}&quot;);" href="{{ route('admin.subscriptions.delete', ['subscription_id' => $subscription->id] ) }}" class="btn btn-danger btn-min-width mr-1 mb-1" title="{{tr('delete')}}"><b>{{tr('delete')}}</b>
								      			</a>
								      	@endif
				            		</li>
			        				
			        			</ul>
			        			<hr>

			        			<div class="card-title">{{tr('description')}}</div>

			        			<span> {{$subscription->description ?: "-"}}</span>
			        			
					        		
				        	</div>

				        	<div class="col-6">

			        			<ul>

			        				<li class="nav-item">
			        					{{tr('name')}}
			        					<span class="float-right">
			        						{{$subscription->title}}
			        					</span>
			        				</li>
			        				<hr>

			        				<li class="nav-item">
			        					{{tr('total_amount')}}
			        					<span class="float-right"> {{$subscription->amount_formatted}}</span>
			        				</li>
			        				<hr>

			        				<li class="nav-item">
			        					{{tr('plan_type')}}
			        					<span class="float-right"> {{$subscription->plan_type_formatted}}</span>
			        				</li>
			        				<hr>

			        				<li class="nav-item">
			        					{{tr('is_popular')}}
			        					@if($subscription->is_popular == YES)
			        						<span class="float-right badge bg-success">{{tr('yes')}}</span>
			        					@else
			        						<span class="float-right badge bg-danger">
			        							{{tr('no')}}
			        						</span>
			        					@endif
			        				</li>
			        				<hr>	        							        		
			        			
			        				<li class="nav-item">
			        					{{tr('is_free')}}
			        					@if($subscription->is_free == YES)
			        						<span class="float-right badge bg-success">{{tr('yes')}}</span>
			        					@else
			        						<span class="float-right badge bg-danger">
			        							{{tr('no')}}
			        						</span>
			        					@endif
			        				</li>
			        				<hr>

			        				<li class="nav-item">
			        					{{tr('status')}}
			        					@if($subscription->status == YES)
			        						<span class="float-right badge bg-success">
			        						{{tr('approved')}}
			        						</span>
			        					@else
			        						<span class="float-right badge bg-danger">
			        							{{tr('declined')}}
			        						</span>
			        					@endif
			        				</li>
			        				<hr>

			        				<li class="nav-item">
			        					{{tr('created_at')}}
			        					<span class="float-right"> {{common_date($subscription->created_at,Auth::guard('admin')->user()->timezone)}}</span>
			        				</li>
			        				<hr>

			        				<li class="nav-item">
			        					{{tr('updated_at')}}
			        					<span class="float-right"> {{common_date($subscription->updated_at,Auth::guard('admin')->user()->timezone)}}</span>
			        				</li>
			        			</ul>
					                
				        	</div>
				            
				      	</div>

				    </div>

				</div>

			</div>

		</div>

	</div>

</section>

@endsection