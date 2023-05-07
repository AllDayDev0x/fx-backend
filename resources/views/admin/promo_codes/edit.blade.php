@extends('layouts.admin')

@section('title', tr('edit_promo_code'))

@section('content-header', tr('promo_code'))

@section('breadcrumb')
	
    <li class="breadcrumb-item"><a href="{{ route('admin.promo_codes.index') }}">{{tr('promo_codes')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{ tr('edit_promo_code') }}</span>
    </li>
           
@endsection 

@section('content')

	@include('admin.promo_codes._form')

@endsection