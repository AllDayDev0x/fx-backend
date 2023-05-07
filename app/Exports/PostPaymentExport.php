<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\PostPayment;
  
class PostPaymentExport implements FromView 
{



    public function __construct(Request $request)
    {
        $this->search_key = $request->search_key;
        $this->user_id = $request->user_id;
        $this->post_id = $request->post_id;
       
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $base_query = PostPayment::orderBy('created_at','desc');

        if($this->post_id) {

            $base_query = $base_query->where('post_id',$this->post_id);
        }

        if($this->search_key) {

            $search_key = $this->search_key;

            $base_query = $base_query
                            ->whereHas('user',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                                
                            })
                            ->orwhereHas('postDetails',function($query) use($search_key){

                                return $query->where('posts.unique_id','LIKE','%'.$search_key.'%');
                            })
                            ->orWhere('post_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        if($this->user_id) {

            $base_query  = $base_query->where('user_id',$this->user_id);
        }

        $base_query = $base_query->whereHas('postDetails')->has('user')->get()->chunk(50);

        return view('exports.post_payment', [
            'data' => $base_query
        ]);

    }

}