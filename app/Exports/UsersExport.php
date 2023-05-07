<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\User;
  
class UsersExport implements FromView 
{



    public function __construct(Request $request)
    {
        $this->search_key = $request->search_key;
        $this->status = $request->status;
        $this->account_type = $request->account_type;
        $this->document_status = $request->document_status;
       
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $base_query = \App\Models\User::orderBy('created_at','desc');

            if($this->search_key) {

                $search_key = $this->search_key;

                $search_user_ids = User::where('users.email', 'LIKE','%'.$search_key.'%')
                                ->orWhere('users.name', 'LIKE','%'.$search_key.'%')
                                ->orWhere('users.mobile', 'LIKE','%'.$search_key.'%')
                                ->pluck('users.id');
                $base_query = $base_query->whereIn('users.id',$search_user_ids);
            }

            if($this->status != '') {

                $base_query = $base_query->where('users.status', $this->status);
            }

            if($this->document_status != '') {

            if($this->document_status == USER_DOCUMENT_PENDING){

                $base_query = $base_query->whereIn('users.is_document_verified',[USER_DOCUMENT_NONE,USER_DOCUMENT_PENDING]);

                
            }else{

                $base_query = $base_query->where('users.is_document_verified',$this->document_status);

            }

        }


            if($this->account_type != '') {

                $base_query = $base_query->where('users.user_account_type', $this->account_type);

            } 
                $base_query = $base_query->get();

    
     return view('exports.users', [
            'data' => $base_query
        ]);


    }

}