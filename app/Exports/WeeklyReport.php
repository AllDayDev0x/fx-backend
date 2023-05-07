<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Http\Request;

use App\Models\User, App\Models\Post, App\Models\UserSubscriptionPayment, App\Models\PostPayment, App\Models\UserTip, App\Models\PostLike, App\Models\Follower;

use Carbon\Carbon;
  
class WeeklyReport implements FromView 
{



    public function __construct(Request $request)
    {
        
        $this->user_id = $request->user_id;
       
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $user_id = $this->user_id;

        $now = Carbon::now();

        $start_date = $now->startOfWeek()->format('Y-m-d H:i:s');

        $end_date = $now->endOfWeek()->format('Y-m-d H:i:s');

        $post = Post::where('user_id',$user_id)->get();

        $data=[];

        $data['posts'] = $post->whereBetween('publish_time', [$start_date, $end_date])->count();

        $post = $post->pluck('id');

        $data['subscription_payment'] = UserSubscriptionPayment::where('to_user_id',$user_id)->whereBetween('paid_date', [$start_date, $end_date])->sum('amount');

        $data['post_payment'] = PostPayment::whereIn('post_id',$post)->whereBetween('paid_date', [$start_date, $end_date])->sum('paid_amount');

        $data['tip_payment'] = UserTip::whereIn('post_id',$post)->whereBetween('paid_date', [$start_date, $end_date])->count();

        $data['likes'] = PostLike::whereIn('post_id',$post)->whereBetween('created_at', [$start_date, $end_date])->count();

        $data['followers'] = Follower::where('user_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->count();

        $data['followings'] = Follower::where('follower_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->count();

        return view('exports.report', [
            'data' => $data
        ]);


    }

    public function send_week_report(Request $request) {

        try {

            $now = Carbon::now();

            $start_date = $now->startOfWeek()->format('Y-m-d H:i:s');

            $end_date = $now->endOfWeek()->format('Y-m-d H:i:s');

            $response = CommonRepo::send_report($start_date,$end_date,$request->user_id,WEELKY_REPORT);

            return redirect()->back()->with('flash_success',tr('report_mail_sent_success'));

        } catch(Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }

    }

}