@extends('layouts.admin') 

@section('title', tr('view_promo_codes'))

@section('content-header', tr('promo_codes'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.promo_codes.index') }}">{{tr('promo_codes')}}</a></li>
    
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_promo_codes') }}</span>
    </li>
    
@endsection 

@section('content')

    <section class="content">
        
        <div class="card">

            <div class="card-header bg-card-header ">

                <h4 class="">{{tr('view_promo_codes')}}</h4>

                    <div class="heading-elements">
                        <a class="btn btn-primary" href="{{route('admin.promo_codes.create')}}">
                            <i class="fa fa-plus"></i> {{tr('add_promo_code')}}
                        </a>
                    </div>


            </div>

            <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                    <div class="row">

                        <div class="col-6"></div>

                        <form class="col-6 row pull-right" action="{{route('admin.promo_codes.index')}}" method="GET" role="search">

                            <div class="input-group">

                                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}"
                                placeholder="{{tr('promo_code_search_placeholder')}}" required> 
                                <span class="input-group-btn">
                                &nbsp
                                <button type="submit" class="btn btn-default">
                                    <span> <i class="fa fa-search" aria-hidden="true"></i>
                                    </span>
                                </button>
                                <a href="{{route('admin.promo_codes.index')}}" class="btn btn-default reset-btn">
                                    <span> <i class="fa fa-eraser" aria-hidden="true"></i>
                                    </span>
                                </a>

                                </span>

                            </div>
                    
                        </form>

                    </div>

                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                        <thead>

                            <tr>
                                <th>{{tr('s_no')}}</th>
                                <th>{{tr('promo_code_title')}}</th>
                                <th>{{tr('content_creator_name')}}</th>
                                <th>{{tr('promo_code')}}</th>
                                <th>{{tr('amount')}}</th>
                                <th>{{tr('amount_type')}}</th>
                                <th>{{tr('status')}}</th>
                                <th>&nbsp;&nbsp;{{tr('action')}}</th>
                            </tr>
                            
                        </thead>

                        <tbody>   

                            @foreach($promo_codes as $i => $promo_code)

                                <tr>

                                    <td>{{$i+$promo_codes->firstItem()}}</td>
                                    
                                    <td>
                                        <a href="{{route('admin.promo_codes.view' , ['promo_code_id' => $promo_code->id] )}}"> 
                                            {{$promo_code->title ?: tr('n_a')}}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $promo_code->user_id] )  }}">
                                            {{ $promo_code->userdisplayname ?: tr('n_a')}}
                                        </a>
                                    </td>

                                    <td>
                                    <a  href="{{ route('admin.promo_codes.view', ['promo_code_id' => $promo_code->id] ) }}">
                                    {{$promo_code->promo_code ?: tr('n_a')}}
                                    </a>
                                    </td>

                                    <td>
                                        @if($promo_code->amount_type == PERCENTAGE)
                                        
                                            {{$promo_code->amount ?: 0}}%
                                        @else

                                            {{$promo_code->amount ?: 0}}

                                        @endif

                                    </td>

                                    <td>
                                        @if($promo_code->amount_type == PERCENTAGE)

                                            <span class="badge badge-success">{{tr('percentage_amount')}}</span>

                                        @else

                                            <span class="badge badge-danger">{{tr('absoulte_amount')}}</span>

                                        @endif
                                    </td>

                                    <td>                                    
                                        @if($promo_code->status == APPROVED)

                                            <span class="btn btn-success btn-sm">
                                                {{ tr('approved') }} 
                                            </span>

                                        @else

                                            <span class="btn btn-danger btn-sm">
                                                {{ tr('declined') }} 
                                            </span>
                                              
                                        @endif
                                    </td>

                                    <td>   
                                        <div class="dropdown">

                                            <button class="btn btn-outline-primary  dropdown-toggle btn-sm" type="button" id="dropdownMenuOutlineButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{tr('action')}}
                                            </button>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">

                                                <a class="dropdown-item" href="{{ route('admin.promo_codes.view', ['promo_code_id' => $promo_code->id] ) }}">
                                                    {{tr('view')}}
                                                </a>

                                                @if(Setting::get('is_demo_control_enabled') == NO)
                                                
                                                    <a class="dropdown-item" href="{{ route('admin.promo_codes.edit', ['promo_code_id' => $promo_code->id] ) }}">
                                                        {{tr('edit')}}
                                                    </a>
                                            
                                                    <a class="dropdown-item" 
                                                    onclick="return confirm(&quot;{{tr('promo_code_delete_confirmation' , $promo_code->name)}}&quot;);" href="{{ route('admin.promo_codes.delete', ['promo_code_id' => $promo_code->id] ) }}" >
                                                        {{ tr('delete') }}
                                                    </a>

                                                @else

                                                    <a class="dropdown-item" href="javascript:;">{{tr('edit')}}</a>

                                                    <a class="dropdown-item" href="javascript:;">{{ tr('delete') }}</a>

                                                @endif

                                                <div class="dropdown-divider"></div>

                                                @if($promo_code->status == APPROVED)

                                                    <a class="dropdown-item" href="{{ route('admin.promo_codes.status', ['promo_code_id' =>  $promo_code->id] ) }}" 
                                                    onclick="return confirm(&quot;{{$promo_code->name}} - {{tr('promo_code_decline_confirmation')}}&quot;);"> 
                                                        {{tr('decline')}}
                                                    </a>

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.promo_codes.status', ['promo_code_id' =>  $promo_code->id] ) }}">
                                                        {{tr('approve')}}
                                                    </a>
                                                       
                                                @endif

                                            </div>
                                             
                                        </div>
                                   
                                    </td>
                                       
                                </tr>
                            
                            @endforeach

                        </tbody>

                    </table> 

                    <div class="pull-right">{{$promo_codes->appends(request()->query())->links('pagination::bootstrap-4')}}</div>                   
                  
                </div>

            </div>

        </div>

        </div>

    </section>

@endsection
