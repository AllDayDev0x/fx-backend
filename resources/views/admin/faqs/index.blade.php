@extends('layouts.admin') 

@section('content-header', tr('faqs')) 

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.faqs.index')}}">{{ tr('faqs') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_faqs') }}</li>
@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_faqs') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_faq') }}</a>
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data table-responsive">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('question') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($faqs as $i => $faq)
                                <tr>
                                    <td>{{ $i+$faqs->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.faqs.view',['faq_id' => $faq->id])}}">
                                        {{ substr($faq->question,0,10)}}...
                                        </a>
                                    </td>

                                    <td>
                                        @if($faq->status == APPROVED)

                                            <span class="badge badge-success">{{tr('approved')}}</span>

                                        @else

                                            <span class="badge badge-danger">{{tr('declined')}}</span>

                                        @endif
                                    </td>
                                    
                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.faqs.view', ['faq_id' => $faq->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.faqs.edit', ['faq_id' => $faq->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('faq_delete_confirmation' , $faq->question) }}&quot;);" href="{{ route('admin.faqs.delete', ['faq_id' => $faq->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($faq->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.faqs.status' , ['faq_id' => $faq->id] )  }}" onclick="return confirm(&quot;{{ $faq->question }} - {{ tr('faq_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.faqs.status' , ['faq_id' => $faq->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $faqs->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection


