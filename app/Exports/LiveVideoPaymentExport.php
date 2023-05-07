<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\LiveVideoPayment;
  
class LiveVideoPaymentExport implements FromView 
{



    public function __construct(Request $request)
    {
        $this->search_key = $request->search_key;
        $this->live_video_id = $request->live_video_id;
        $this->user_id = $request->user_id;
       
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $base_query = LiveVideoPayment::orderBy('created_at','DESC');

        if($this->live_video_id) {

            $base_query = $base_query->where('live_video_id',$this->live_video_id);
        }

        if($this->search_key) {

            $search_key = $this->search_key;

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

        if($this->user_id) {

            $base_query  = $base_query->where('user_id',$this->user_id);
        }

        $base_query = $base_query->whereHas('videoDetails')->has('user')->get()->chunk(50);

        return view('exports.live_video_payment', [
            'data' => $base_query
        ]);

    }

}