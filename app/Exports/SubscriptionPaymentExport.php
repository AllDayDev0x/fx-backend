<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\UserSubscriptionPayment;
  
class SubscriptionPaymentExport implements FromView 
{



    public function __construct(Request $request)
    {
        $this->search_key = $request->search_key;
        $this->from_user_id = $request->from_user_id;
        $this->status = $request->status;
       
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $base_query = UserSubscriptionPayment::has('fromUser')->has('toUser');

        $search_key = $this->search_key;

        if($search_key) {

            $base_query = $base_query
                        ->whereHas('fromUser',function($query) use($search_key) {

                            return $query->where('users.name','LIKE','%'.$search_key.'%');

                        })->orwhereHas('toUser',function($query) use($search_key) {
                            
                            return $query->where('users.name','LIKE','%'.$search_key.'%');
                        });
        }

        $user = '';

        if($this->from_user_id){

            $base_query->where('from_user_id',$this->from_user_id);
        }


        if($this->status) {

            switch ($this->status) {

                case SORT_BY_HIGH:
                    $base_query = $base_query->orderBy('amount','desc');
                    break;

                case SORT_BY_LOW:
                    $base_query = $base_query->orderBy('amount','asc');
                    break;

                case SORT_BY_FREE:
                    $base_query = $base_query->where('amount',0.00);
                    break;

                case SORT_BY_PAID:

                    $base_query = $base_query->where('amount','!=',0.00);

                    break;
               
                default:
                    $base_query = $base_query->orderBy('created_at','desc');
                    
                    break;
            }
        }
        else{

            $base_query = $base_query->orderBy('created_at','desc');
        }

        $base_query = $base_query->get()->chunk(50);

        return view('exports.subscription_payment', [
            'data' => $base_query
        ]);

    }

}