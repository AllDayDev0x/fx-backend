<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\VideoCallPayment;
  
class VideoCallPaymentExport implements FromView 
{



    public function __construct(Request $request)
    {
        $this->search_key = $request->search_key;
        $this->user_id = $request->user_id;
       
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $base_query = VideoCallPayment::where('status',APPROVED);

        if($this->user_id){

            $base_query->where('user_id',$this->user_id);
        }

        if($this->search_key) {

            $search_key = $this->search_key;

            $base_query = $base_query
                            ->whereHas('user',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })
                            ->orWhereHas('model',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })
                            ->orWhere('video_call_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $base_query = $base_query->orderBy('created_at','DESC')->get()->chunk(50);

        return view('exports.video_call_payment', [
            'data' => $base_query
        ]);

    }

}