<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor,Log;

use App\Jobs\SendEmailJob;

use App\Jobs\PublishPostJob;

use Carbon\Carbon;

use Excel;

use App\Exports\LiveVideoPaymentExport;

class AdminLiveVideoController extends Controller
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
     * @method live_videos_index()
     *
     * @uses Display the Live Videos
     *
     * @created Ganesh
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function live_videos_index(Request $request) {

        $base_query = \App\Models\LiveVideo::orderBy('created_at','DESC');

        if($request->payment_status !='') {

            $base_query->where('payment_status',$request->payment_status);
        }

        if($request->video_type) {

            $base_query->where('type',$request->video_type);
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $live_video_ids = \App\Models\LiveVideo::whereHas('user', function($q) use ($search_key) {

                return $q->Where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('live_videos.title','LIKE','%'.$search_key.'%')->pluck('id');

            $base_query = $base_query->whereIn('id',$live_video_ids);

        }

        $live_videos = $base_query->whereHas('user')->paginate(10);

        $live_videos->title = tr('live_videos');

        return view('admin.live_videos.index')
                ->with('page', 'live-videos')
                ->with('sub_page', 'live-videos-history')
                ->with('is_streaming', NO)
                ->with('live_videos', $live_videos);
    
    }


    /**
     * @method videos_index()
     *
     * @uses To list out LiveVideos
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function live_videos_onlive(Request $request) {
        
        $base_query = \App\Models\LiveVideo::where('live_videos.status',VIDEO_STREAMING_ONGOING)->where('live_videos.is_streaming', IS_STREAMING_YES)
            ->orderBy('live_videos.created_at', 'desc');

        if($request->payment_status !='') {

            $base_query->where('live_videos.payment_status',$request->payment_status);
        }

        if($request->video_type) {

            $base_query->where('live_videos.type',$request->video_type);
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $live_video_ids = \App\Models\LiveVideo::whereHas('user', function($q) use ($search_key) {

                return $q->Where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('live_videos.title','LIKE','%'.$search_key.'%')->pluck('id');

            $base_query = $base_query->whereIn('id',$live_video_ids);

        }
        
        $live_videos = $base_query->paginate(10);
        
        $live_videos->title = tr('live_videos_history');

        return view('admin.live_videos.index')
                ->with('page', 'live-videos')
                ->with('sub_page', 'live-videos-live')
                ->with('is_streaming', YES)
                ->with('live_videos', $live_videos);
    }

    /**
     * @method live_videos_delete()
     *
     * To delete a live streaming video which is stopped by the user
     *
     * @created Ganesh
     *
     * @updated by - 
     *
     * @param integer $request - Video id
     *
     * @return repsonse of success/failure message
     */
    public function live_videos_delete(Request $request) {

        try {

            $live_video = \App\Models\LiveVideo::find($request->live_video_id);

            if(!$live_video){

                throw new Exception(tr('live_video_not_found'));

            }
            
            if($live_video->status== VIDEO_STREAMING_ONGOING){

                throw new Exception(tr('broadcast_video_delete_failure'));
            }

            DB::beginTransaction();

            if ($live_video) {                

                $live_video->delete();

                DB::commit();

                return back()->with('flash_success', tr('live_video_delete_success'));

            } 

           throw new Exception(tr('live_video_not_found'));
                

        } catch(Exception $e) {

            DB::rollback();

            return back()->with('flash_error', $e->getMessage());

        }

    } 


     /**
     * @method live_videos_view()
     *
     * @uses displays the specified live video details based on live video id
     *
     * @created Ganesh 
     *
     * @updated 
     *
     * @param object $request - post Id
     * 
     * @return View page
     *
     */
    public function live_videos_view(Request $request) {

        try {
            
            $live_video = \App\Models\LiveVideo::find($request->live_video_id);
            
            if(!$live_video) { 

                throw new Exception(tr('live_video_not_found'), 101);                
            }

            if(Setting::get('is_only_wallet_payment')){
                
                $live_video_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                    ->where('token', '>', 0)
                    ->sum('token');

                $user_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                    ->where('token', '>', 0)
                    ->sum('user_token');

                $admin_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                    ->where('token', '>', 0)
                    ->sum('admin_token');

            } else {
            
                $live_video_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                        ->where('amount', '>', 0)
                        ->sum('amount');

                $user_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                        ->where('amount', '>', 0)
                        ->sum('user_amount');

                $admin_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                        ->where('amount', '>', 0)
                        ->sum('admin_amount');

            }

            $live_video->live_video_amount = formatted_amount($live_video_amount ?? 0);

            $live_video->user_amount = formatted_amount($user_amount ?? 0);

            $live_video->admin_amount = formatted_amount($admin_amount ?? 0);

            return view('admin.live_videos.view')
                ->with('page', 'live-videos')
                ->with('sub_page', 'live-videos-history')
                ->with('live_video', $live_video);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method live_video_payments()
     *
     * @uses Display the lists of post payments
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function live_video_payments(Request $request) {

        $base_query = \App\Models\LiveVideoPayment::orderBy('created_at','DESC');

        $title = tr('live_video_payments');

        if($request->live_video_id) {

            $base_query = $base_query->where('live_video_id',$request->live_video_id);

            $live_video = \App\Models\LiveVideo::find($request->live_video_id);

            $title = tr('live_video_payments')." - ".$live_video->title;
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query
                            ->whereHas('user',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                                
                            })
                            ->orwhereHas('videoDetails',function($query) use($search_key){

                                return $query->where('live_videos.unique_id','LIKE','%'.$search_key.'%');
                            })
                            ->orwhereHas('videoDetails',function($query) use($search_key){

                                return $query->where('live_videos.title','LIKE','%'.$search_key.'%');
                            })
                            ->orWhere('live_video_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $user = \App\Models\User::find($request->user_id) ?? '';

        if($request->user_id) {

            $base_query  = $base_query->where('user_id',$request->user_id)->orWhere('live_video_viewer_id',$request->user_id);
        }

        $live_video_payments = $base_query->whereHas('videoDetails')->has('user')->orderBy('created_at','DESC')->paginate(10);
        
        return view('admin.live_videos.payments')
                ->with('page','payments')
                ->with('sub_page','live-video-payments')
                ->with('user',$user)
                ->with('title', $title)
                ->with('live_video_payments',$live_video_payments);
    }


    /**
     * @method post_payments_view()
     *
     * @uses 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function live_video_payments_view(Request $request) {

        try {

            $live_video_payment = \App\Models\LiveVideoPayment::where('id',$request->live_video_payment_id)->first();

            if(!$live_video_payment) {

                throw new Exception(tr('post_payment_not_found'), 1);
                
            }
           
            return view('admin.live_videos.payments_view')
                    ->with('page','payments')
                    ->with('sub_page','live-video-payments')
                    ->with('live_video_payment',$live_video_payment);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

    public function live_video_payment_excel(Request $request) {

        try{
            $file_format = $request->file_format ?? '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new LiveVideoPaymentExport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.live_videos.payments')->with('flash_error' , $e->getMessage());

        }

    }

}
