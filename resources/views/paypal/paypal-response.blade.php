<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{Setting::get('site_name')}}</title>

	<style type="text/css">
			
		@media screen and (max-width: 575px) {

			body,html{
			    height: 100%;
			    width:100%;
			    padding:0;
			    margin:0; 
			    color: #484848;
			    background-color: #fff;
			    overflow-x:hidden;
			    font-size: 14px;
			}
		}
		.wrapper{
		    min-height: 100%;
		    margin:0;
		    padding: 0;
		    position: relative;
		    padding: 20px;
		}
		.success-img{
			max-width: 100%;
			object-fit: contain;
			margin: 0 auto;
			margin-top: 10px;
		}
		.text-center{
			text-align: center;
		}
		.payment-head{
			margin-top: 20px;
			font-size: 20px;
			text-transform: capitalize;
			margin-bottom: 10px;
		}
		.card{
			background-color: #fff;
			padding: 10px;
			border-radius:3px;
			box-shadow: rgba(0, 0, 0, 0.15) 0px 2px 4px 0px !important;
		    -moz-box-shadow:rgba(0, 0, 0, 0.15) 0px 2px 4px 0px !important;
		    -webkit-box-shadow:rgba(0, 0, 0, 0.15) 0px 2px 4px 0px !important;
		}
		.red-clr{
			color: #e51717;
			margin-top: 0;
			margin-bottom: 10px;
		}
		.plan-title{
			color: #484848;
			margin-top: 0;
			margin-bottom: 0;
			text-transform: capitalize;
		}
		.top{
			margin-top: 15px;
			text-transform: capitalize;
		}
		.success-btn{
			display: inline-block;
			padding:8px 15px;
			background-color: #3b5887;
			color: #fff;
			border-radius:3px;
			box-shadow: rgba(0, 0, 0, 0.15) 0px 2px 4px 0px !important;
		    -moz-box-shadow:rgba(0, 0, 0, 0.15) 0px 2px 4px 0px !important;
		    -webkit-box-shadow:rgba(0, 0, 0, 0.15) 0px 2px 4px 0px !important;
		    margin-top: 15px;
		    text-transform: capitalize;
		}
		.success-btn:hover, .success-btn:active, .success-btn:focus{
			color: #fff;
			box-shadow: rgba(0, 0, 0, 0.25) 0px 2px 4px 0px !important;
		    -moz-box-shadow:rgba(0, 0, 0, 0.25) 0px 2px 4px 0px !important;
		    -webkit-box-shadow:rgba(0, 0, 0, 0.25) 0px 2px 4px 0px !important;
		}
		.failure-btn{
			display: inline-block;
			padding:8px 15px;
			background-color: #f0ad4e;
			color: #fff;
			border-radius:3px;
			box-shadow: rgba(0, 0, 0, 0.15) 0px 2px 4px 0px !important;
		    -moz-box-shadow:rgba(0, 0, 0, 0.15) 0px 2px 4px 0px !important;
		    -webkit-box-shadow:rgba(0, 0, 0, 0.15) 0px 2px 4px 0px !important;
		    margin-top: 15px;
		    text-transform: capitalize;
		}
		.failure-btn:hover, .failure-btn:active, .failure-btn:focus{
			color: #fff;
			box-shadow: rgba(0, 0, 0, 0.25) 0px 2px 4px 0px !important;
		    -moz-box-shadow:rgba(0, 0, 0, 0.25) 0px 2px 4px 0px !important;
		    -webkit-box-shadow:rgba(0, 0, 0, 0.25) 0px 2px 4px 0px !important;
		}
		.text-right{
			text-align: right;
		}
		
	</style>
</head>

<body>
	
	<div class="wrapper">
		
		@if($data['success'])

			<div class="text-center">
				<img src="{{asset('payment_success.png')}}" class="success-img">
				<h4 class="payment-head">{{tr('payment_success')}}</h4>
				<!-- <p>{{tr('payment_success')}}</p> -->

			</div>

			<div class="card">
				<p class="red-clr">{{tr('description')}}</p>
				<h4 class="plan-title">{{$data['message']}}</h4>
			</div>

			<div class="card top">
				<p class="red-clr">{{tr('total')}}</p>
				<h4 class="plan-title">{{formatted_amount($data['amount'])}}</h4>
			</div>


			<div class="text-right">
				<button class="success-btn" type="button" onclick="return success()">{{tr('success')}}</button>
			</div>
		
		@else
			<div class="text-center">
				<img src="{{asset('payment_failure.jpg')}}" class="success-img">
				<h4 class="payment-head">{{tr('payment_failure')}}</h4>
				<p>{{$data['error_messages']}}</p>
			</div>

			<div class="text-right">
				<button class="failure-btn" type="button" onclick="return error()">{{tr('failure')}}</button>
			</div>

		@endif

		<script type="text/javascript">

			@if($data['success'])

				function success() {
					
					alert("{{$data['message']}}");

					callNativeApp();

				}

				function callNativeApp () {
				   try {
				       webkit.messageHandlers.callbackHandler.postMessage("{{$data['message']}}");

				   } catch(err) {
				       console.log('The native context does not exist yet');
				   }
				}

			@else

				function error() {

					alert("{{$data['error_messages']}}");

					callNativeApp();

				}

				function callNativeApp () {
				   try {
				       webkit.messageHandlers.callbackHandler.postMessage("{{$data['error_messages']}}");

				   } catch(err) {
				       console.log('The native context does not exist yet');
				   }
				}
				
			@endif
			
			if (/Mobi|Android|iPad|iPhone|iPod/i.test(navigator.userAgent)) {
				console.log("NO WEB");
			} else {

				console.log("Is WEB");

				@php $redirect_url = Setting::get('frontend_url'); @endphp

				var redirect_url = "{{$redirect_url}}";

				console.log("redirect_url"+redirect_url);

				setTimeout(function() {

					window.location = "{{$redirect_url}}";

				}, 5000);
			}

			

		</script>

	</div>
</body>
</html>