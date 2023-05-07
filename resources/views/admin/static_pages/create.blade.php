@extends('layouts.admin')

@section('title', tr('add_static_page'))

@section('content-header',tr('static_pages'))

@section('breadcrumb')

    
    <li class="breadcrumb-item">
    	<a href="{{ route('admin.static_pages.index') }}">{{tr('static_pages')}}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('add_static_page')}}</span>
    </li>
           
@endsection 

@section('content')
    
    @include('admin.static_pages._form')

@endsection

@section('scripts')

<script src="{{asset('js/summernote.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
  </script>

@endsection
