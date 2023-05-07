@extends('layouts.admin')

@section('title', tr('add_hashtag'))

@section('content-header',tr('hashtags'))

@section('styles')

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

@endsection

@section('breadcrumb')

    
    <li class="breadcrumb-item">
    	<a href="{{ route('admin.hashtags.index') }}">{{tr('hashtags')}}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('add_hashtag')}}</span>
    </li>
           
@endsection 

@section('content')
    
    @include('admin.hashtags._form')

@endsection

@section('scripts')

<script src="{{asset('js/summernote.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
  </script>

@endsection
