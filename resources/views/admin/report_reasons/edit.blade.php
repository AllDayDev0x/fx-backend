@extends('layouts.admin')

@section('title', tr('edit_report_reason'))

@section('content-header',tr('report_reasons'))

@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{ route('admin.report_reasons.index') }}">{{tr('report_reasons')}}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('edit_report_reason')}}</span>
    </li>
           
@endsection 

@section('content')
    
    @include('admin.report_reasons._form')

@endsection

@section('scripts')

<script src="{{asset('js/summernote.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
  </script>

@endsection
