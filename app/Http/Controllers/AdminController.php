<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Models\Admin, App\Models\User, App\Stardom, App\Models\Document, App\Models\UserDocument, App\Models\UserProduct;

use App\Models\Settings, App\Models\StaticPage;

use App\Jobs\SendEmailJob;

use Carbon\Carbon;

class AdminController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

        $this->middleware('auth:admin');

        $this->skip = $request->skip ?: 0;
       
        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }
}
