<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\ChatAssetPayment;
  
class ChatAssetPaymentExport implements FromView 
{



    public function __construct(Request $request)
    {
        $this->search_key = $request->search_key;
        $this->from_user_id = $request->from_user_id;
       
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $base_query = ChatAssetPayment::orderBy('created_at','desc');


        if($this->from_user_id){

            $base_query->where('from_user_id',$this->from_user_id);
        }

        $search_key = $this->search_key;

        if($search_key) {

            $base_query = $base_query
                        ->whereHas('fromUser',function($query) use($search_key) {

                            return $query->where('users.name','LIKE','%'.$search_key.'%');

                        })->orwhereHas('toUser',function($query) use($search_key) {
                            
                            return $query->where('users.name','LIKE','%'.$search_key.'%');
                        });
        }

        $base_query = $base_query->get()->chunk(50);

        return view('exports.chat_asset_payment', [
            'data' => $base_query
        ]);

    }

}