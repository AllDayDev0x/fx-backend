@extends('layouts.admin')

@section('content-header', tr('subscriptions'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index') }}">{{tr('subscriptions')}}</a></li>
    
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{ tr('edit_subscription') }}</span>
    </li>
           
@endsection 

@section('content')

	@include('admin.subscriptions._form')

@endsection