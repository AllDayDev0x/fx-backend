@extends('layouts.admin')

@section('title', tr('documents'))

@section('content-header', tr('documents'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.documents.index')}}">{{tr('documents')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('edit_document')}}</a></li>

@endsection

@section('content')

    @include('admin.documents._form')

@endsection

