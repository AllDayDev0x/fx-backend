<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor,Log;

use App\Jobs\SendEmailJob;

use Carbon\Carbon;

use Excel;

use App\Exports\VideoCallPaymentExport;

use App\Models\User;

class AdminVideoCallRequestController extends Controller
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

     /**
     * @method video_call_requests_index()
     *
     * @uses To list out video call requests
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function video_call_requests_index(Request $request) {

      try {

        $base_query = \App\Models\VideoCallRequest::orderBy('created_at','desc');

        $sub_page = 'video-call-requests';

        
        if($request->user_id){

            $base_query->where('user_id',$request->user_id);
        }

        if(isset($request->status)) {

            $base_query = $base_query->where('video_call_requests.call_status', $request->status);

        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query
                            ->whereHas('user',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })
                            ->orwhereHas('model',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })->orWhere('video_call_requests.unique_id','LIKE','%'.$search_key.'%');
                            
        }

        $video_call_requests = $base_query->paginate($this->take);       
        
        return view('admin.video_call_requests.index')
                    ->with('page','one-to-one')
                    ->with('sub_page', $sub_page)
                    ->with('video_call_requests', $video_call_requests);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }


    /**
     * @method video_call_requests_view()
     *
     * @uses Display the specified video call requests details
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function video_call_requests_view(Request $request) {

        try {

            $video_call_request = \App\Models\VideoCallRequest::where('id',$request->video_call_request_id)->first();

            if(!$video_call_request) {

                throw new Exception(tr('video_call_request_not_found'), 101);
                
            }

            $video_call_payment = \App\Models\VideoCallPayment::where('video_call_request_id',$video_call_request->id)->where('status',PAID)->first();

            $video_call_request->payment_status = $video_call_payment ? YES:NO;

            return view('admin.video_call_requests.view')
                    ->with('page','one-to-one')
                    ->with('sub_page','video-call-requests')
                    ->with('video_call_request',$video_call_request)
                    ->with('video_call_payment',$video_call_payment);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

    /**
     * @method video_call_payments()
     *
     * @uses Display the lists of video call payments
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function video_call_payments(Request $request) {

      try {

        $base_query = \App\Models\VideoCallPayment::where('status',APPROVED);


        if($request->user_id){

            $base_query->where('user_id',$request->user_id);
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query
                            ->whereHas('user',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })
                            ->orWhereHas('model',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })
                            ->orWhere('video_call_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $video_call_payments = $base_query->orderBy('created_at','DESC')->paginate(10);

        $user = User::find($request->user_id)??'';
       
        return view('admin.revenues.video_call_payments.index')
                ->with('page','payments')
                ->with('sub_page','video-call-payments')
                ->with('video_call_payments',$video_call_payments)
                ->with('user',$user);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }


    /**
     * @method video_call_payments_view()
     *
     * @uses Display the specified video call payment details
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function video_call_payments_view(Request $request) {

        try {

            $video_call_payment = \App\Models\VideoCallPayment::where('id',$request->video_call_payment_id)->first();

            if(!$video_call_payment) {

                throw new Exception(tr('video_call_payment_not_found'), 101);
                
            }
           
            return view('admin.revenues.video_call_payments.view')
                    ->with('page','payments')
                    ->with('sub_page','video-call-payments')
                    ->with('video_call_payment',$video_call_payment);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

    public function video_call_payment_excel(Request $request) {

        try{
            $file_format = $request->file_format ?? '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new VideoCallPaymentExport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.video_call_payments.index')->with('flash_error' , $e->getMessage());

        }

    }

}
