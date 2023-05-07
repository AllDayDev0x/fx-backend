@extends('layouts.admin')

@section('title', tr('add_category'))

@section('content-header', tr('categories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.categories.index')}}">{{tr('categories')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_category')}}</a></li>

@endsection

@section('content')

    @include('admin.categories._form')

@endsection
