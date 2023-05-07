<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\OrderPayment;
  
class OrderPaymentExport implements FromView 
{



    public function __construct(Request $request)
    {
        $this->search_key = $request->search_key;
        $this->order_id = $request->order_id;
       
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $base_query = OrderPayment::where('status',APPROVED);

        if($this->order_id) {

            $base_query = $base_query->where('order_id',$this->order_id);
        }

        if($this->search_key) {

            $search_key = $this->search_key;

            $base_query = $base_query
                            ->whereHas('user',function($query) use($search_key){

                                return $query->where('users.name','LIKE','%'.$search_key.'%');
                            })->orWhere('order_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $base_query = $base_query->get()->chunk(50);

        return view('exports.order_payment', [
            'data' => $base_query
        ]);

    }

}