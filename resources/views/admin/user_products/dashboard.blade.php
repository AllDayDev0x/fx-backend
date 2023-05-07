@extends('layouts.admin')

@section('content-header',tr('product_dashboard'))

@section('breadcrumb')

    
    <li class="breadcrumb-item"><a href="{{route('admin.user_products.index')}}">{{tr('user_products')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('product_dashboard')}}</a>
    </li>

@endsection

@section('content')
<section class="content">

    <div class="row">

	    <div class="col-xl-3 col-lg-6 col-12">

	        <div class="card">

	            <div class="card-content">

	                <div class="card-body">

	                    <div class="media">
	                    	
	                    	<a href="{{route('admin.order_products',['user_product_id'=>$user_product->id ?? ''])}}">
	                        	<div class="media-body text-left w-100">
	                            	<h3 class="primary">{{$data->total_orders}}</h3>
	                            	<span>{{tr('total_orders')}}</span>
		                        </div>
		                        <div class="media-right media-middle">
		                            <i class="icon-basket-loaded primary font-large-2 float-right"></i>
		                        </div>
	                        </a>

	                    </div>

	                    <div class="progress progress-sm mt-1 mb-0">
	                        <div class="progress-bar bg-primary" role="progressbar" style="width: 80%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
	                    </div>

	                </div>

	            </div>

	        </div>

	    </div>

	    <div class="col-xl-3 col-lg-6 col-12">

	        <div class="card">

	            <div class="card-content">

	                <div class="card-body">

	                    <div class="media">
	                        <div class="media-body text-left w-100">
	                            <h3 class="danger">{{$data->today_orders}}</h3>
	                            <span>{{tr('today_orders')}}</span>
	                        </div>
	                        <div class="media-right media-middle">
	                            <i class="icon-layers danger font-large-2 float-right"></i>
	                        </div>
	                    </div>

	                    <div class="progress progress-sm mt-1 mb-0">
	                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
	                    </div>

	                </div>

	            </div>

	        </div>

	    </div>

	    <div class="col-xl-3 col-lg-6 col-12">

	        <div class="card">

	            <div class="card-content">

	                <div class="card-body">

	                    <div class="media">
	                        <div class="media-body text-left w-100">
	                            <h3 class="success">{{formatted_amount($data->total_revenue)}}</h3>
	                            <span>{{tr('total_revenue')}}</span>
	                        </div>
	                        <div class="media-right media-middle">
	                            <i class="icon-wallet success font-large-2 float-right"></i>
	                        </div>
	                    </div>

	                    <div class="progress progress-sm mt-1 mb-0">
	                        <div class="progress-bar bg-success" role="progressbar" style="width: 60%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
	                    </div>

	                </div>

	            </div>

	        </div>

	    </div>

	    <div class="col-xl-3 col-lg-6 col-12">

	        <div class="card">

	            <div class="card-content">

	                <div class="card-body">

	                    <div class="media">
	                        <div class="media-body text-left w-100">
	                            <h3 class="warning">{{formatted_amount($data->today_revenue)}}</h3>
	                            <span>{{tr('today_revenue')}}</span>
	                        </div>
	                        <div class="media-right media-middle">
	                            <i class="icon-globe warning font-large-2 float-right"></i>
	                        </div>
	                    </div>

	                    <div class="progress progress-sm mt-1 mb-0">
	                        <div class="progress-bar bg-warning" role="progressbar" style="width: 35%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
	                    </div>

	                </div>

	            </div>

	        </div>

	    </div>
	   
        <div class="col-xl-12 col-lg-6 col-12">

            <div class="card">

                <div class="card-body " id="chartjs_line_parent">

                	<div class="card-title">{{tr('order_payment')}} - <a href="{{route('admin.user_products.view',['user_product_id' => $user_product->id])}}">{{$user_product->name ?: tr('n_a')}}</a>	</h4></div>

                    <div class="row">

                        <canvas id="chartjs_line"></canvas>

                    </div>

                </div>

            </div>

        </div>

	</div>

</section>

@endsection

@section('scripts')
    
    <script src="{{asset('admin-assets/graph-assets/plugins/chart-js/Chart.bundle.js')}}" ></script>

    <script src="{{asset('admin-assets/graph-assets/plugins/chart-js/utils.js')}}" ></script>
    
    <script type="text/javascript">

    	$(document).ready(function() {

			var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

		    var config = {
		        type: 'line',
		        data: {
		            labels: [<?php foreach ($data->analytics->last_x_days_revenues as $key => $value) 		{
	                    echo '"'.$value->date.'"'.',';
	                } 
	             	?>],
		            datasets: [{

		                label: "Earnings",

		                backgroundColor: window.chartColors.red,

		                borderColor: window.chartColors.red,

		                data: [
		                   <?php 
                                foreach ($data->analytics->last_x_days_revenues as $value) {
                                    echo $value->total_earnings.',';
                                }

                            ?>
		                ],
		                fill: true,
		            }]
		        },
		        options: {
		            responsive: true,
		            title:{
		                display:true,
		                text:'Product Payment'
		            },
		            tooltips: {
		                mode: 'index',
		                intersect: false,
		            },
		            hover: {
		                mode: 'nearest',
		                intersect: true
		            },
		            scales: {
		                xAxes: [{
		                    display: true,
		                    scaleLabel: {
		                        display: true,
		                        labelString: 'Last 7 days'
		                    }
		                }],
		                yAxes: [{
		                    display: true,
		                    scaleLabel: {
		                        display: true,
		                        labelString: 'Amount'
		                    }
		                }]
		            }
		        }
		    };

		    var ctx = document.getElementById("chartjs_line").getContext("2d");

		    window.myLine = new Chart(ctx, config);
		});
  
    </script>
@endsection
