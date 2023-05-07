@extends('layouts.admin')

@section('title', tr('vod'))

@section('content-header', tr('vod'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.vod_videos.index')}}">{{tr('vod')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('edit_vod')}}</a></li>

@endsection

@section('content')

    @include('admin.vod_videos._form')

@endsection