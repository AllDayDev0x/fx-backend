@extends('layouts.admin')

@section('title', tr('support_members'))

@section('content-header', tr('support_members'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.support_members.index')}}">{{tr('support_members')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('edit_support_member')}}</a></li>

@endsection

@section('content')

    @include('admin.support_members._form')

@endsection
