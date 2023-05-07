<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Jobs\SendEmailJob;

use App\Models\AudioCallRequest, App\Models\AudioCallPayment, App\Models\User;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor,Log;

use Carbon\Carbon;

use Excel;

use App\Exports\AudioCallPaymentExport;

class AdminAudioCallRequestController extends Controller
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
     * @method audio_call_payments_view()
     *
     * @uses Display the specified audio call payment details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function audio_call_payments_view(Request $request) {

        try {

            $audio_call_payment = AudioCallPayment::where('id',$request->audio_call_payment_id)->first();

            if(!$audio_call_payment) {

                throw new Exception(tr('audio_call_payment_not_found'), 101);
                
            }
           
            return view('admin.revenues.audio_call_payments.view')
                    ->with('page','payments')
                    ->with('sub_page','audio-call-payments')
                    ->with('audio_call_payment',$audio_call_payment);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

    /**
     * @method audio_call_requests_view()
     *
     * @uses Display the specified audio call requests details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function audio_call_requests_view(Request $request) {

        try {

            $audio_call_request = AudioCallRequest::where('id',$request->audio_call_request_id)->first();

            if(!$audio_call_request) {

                throw new Exception(tr('audio_call_request_not_found'), 101);
                
            }

            $audio_call_payment = AudioCallPayment::where('audio_call_request_id',$audio_call_request->id)->where('status',PAID)->first();

            $audio_call_request->payment_status = $audio_call_payment ? YES:NO;

            return view('admin.audio_call_requests.view')
                    ->with('page','one-to-one')
                    ->with('sub_page','audio-call-requests')
                    ->with('audio_call_request',$audio_call_request)
                    ->with('audio_call_payment',$audio_call_payment);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

     /**
     * @method audio_call_requests_index()
     *
     * @uses To list out audio call requests
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function audio_call_requests_index(Request $request) {

      try {

        $base_query = AudioCallRequest::orderBy('created_at','desc');
        
        if($request->user_id){

            $base_query->where('user_id',$request->user_id);
        }

        if(isset($request->status)) {

            $base_query = $base_query->where('audio_call_requests.call_status', $request->status);

        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query
                            ->whereHas('user',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })
                            ->orwhereHas('model',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })->orWhere('audio_call_requests.unique_id','LIKE','%'.$search_key.'%');
                            
        }

        $audio_call_requests = $base_query->paginate($this->take);
        
        return view('admin.audio_call_requests.index')
                    ->with('page','one-to-one')
                    ->with('sub_page', 'audio-call-requests')
                    ->with('audio_call_requests', $audio_call_requests);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method audio_call_payments()
     *
     * @uses Display the lists of audio call payments
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function audio_call_payments(Request $request) {

      try {

        $base_query = AudioCallPayment::where('status',APPROVED);


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
                            ->orWhere('audio_call_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $audio_call_payments = $base_query->orderBy('created_at','DESC')->paginate(10);

        $user = User::find($request->user_id)??'';
       
        return view('admin.revenues.audio_call_payments.index')
                ->with('page','payments')
                ->with('sub_page','audio-call-payments')
                ->with('audio_call_payments',$audio_call_payments)
                ->with('user',$user);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

    public function audio_call_payment_excel(Request $request) {

        try{
            $file_format = $request->file_format ?? '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new AudioCallPaymentExport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.audio_call_payments.index')->with('flash_error' , $e->getMessage());

        }

    }

}
