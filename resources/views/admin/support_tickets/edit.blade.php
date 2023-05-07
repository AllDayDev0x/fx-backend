@extends('layouts.admin') 

@section('content-header', tr('support_tickets'))

@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{ route('admin.support_tickets.index') }}">{{tr('support_tickets')}}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('edit_support_tickets')}}</span>
    </li>
           
@endsection 

@section('content') 

	@include('admin.support_tickets._form') 

@endsection