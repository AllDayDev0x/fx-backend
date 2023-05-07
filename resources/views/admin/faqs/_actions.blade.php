
<ul class="navbar-nav float-right">

    <li class="nav-item dropdown">

      <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-cog"></i>
      </a>
      
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-dark bg-default  dropdown-menu-right ">

        <div class="row shortcuts px-4">

        	@if(Setting::get('is_demo_control_enabled') == YES)

	            <a class="col-4 shortcut-item" href="javascript:void(0)">
	            	<span class="shortcut-media avatar rounded-circle bg-gradient-green"><i class="fa fa-edit" aria-hidden="true"></i></span>
	            	<small>{{tr('edit')}}</small>
	            </a>

	            <a class="col-4 shortcut-item" href="javascript:void(0)">
	            	<span class="shortcut-media avatar rounded-circle bg-gradient-red"><i class="fa fa-trash"></i>
	            	</span>
	            	<small>{{tr('delete')}}</small>
	            </a>

	        @else
	        
	            <a class="col-4 shortcut-item" href="{{ route('admin.faqs.edit', ['faq_id' => $faq->id] ) }}"> 
	            	<span class="shortcut-media avatar rounded-circle bg-gradient-green"><i class="fa fa-edit" aria-hidden="true"></i></span>
	            	<small>{{tr('edit')}}</small>
	            </a>

	            <a class="col-4 shortcut-item" onclick="return confirm(&quot;{{ tr('faq_delete_confirmation', $faq->question ) }}&quot;);" href="{{ route('admin.faqs.delete', ['faq_id' => $faq->id] ) }}"><span class="shortcut-media avatar rounded-circle bg-gradient-red"><i class="fa fa-trash"></i>
	            	</span>
	            	<small>{{tr('delete')}}</small>
	            </a>

	        @endif

        </div>

      </div>

    </li>

</ul>
