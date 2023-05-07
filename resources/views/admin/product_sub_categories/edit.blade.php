@extends('layouts.admin')

@section('title', tr('edit_product_sub_category'))

@section('content-header', tr('product_sub_categories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.product_sub_categories.index')}}">{{tr('product_sub_categories')}}</a></li>

    <li class="breadcrumb-item active">{{tr('edit_product_sub_category')}}</a></li>

@endsection

@section('content')

    @include('admin.product_sub_categories._form')

@endsection