@extends('layouts.admin')

@section('title', tr('faqs'))

@section('content-header', tr('faqs'))

@section('styles')

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

@endsection

@section('breadcrumb')

	
    

    <li class="breadcrumb-item"><a href="{{route('admin.faqs.index')}}">{{tr('faqs')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_faq')}}</a></li>

@endsection

@section('content')

    @include('admin.faqs._form')

@endsection

@section('scripts')

<script src="{{asset('js/summernote.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
  </script>

@endsection
