@extends('layouts.admin')

@section('title', tr('stories'))

@section('content-header', tr('stories'))

@section('styles')

    <link rel="stylesheet" href="{{asset('admin-assets/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">

@endsection


@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{route('admin.stories.index')}}">{{tr('vods')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('create_vods')}}</a></li>


@endsection

@section('content')

    @include('admin.vod_videos._form')

@endsection