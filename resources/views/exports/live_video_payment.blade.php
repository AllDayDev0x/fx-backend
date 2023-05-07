<!DOCTYPE html>
<html>

<head>
    <title>{{tr('live_video_payments')}}</title>
</head>
<style type="text/css">

    table{
        font-family: arial, sans-serif;
        border-collapse: collapse;
    }

    .first_row_design{
        background-color: #187d7d;
        color: #ffffff;
    }

    .row_col_design{
        background-color: #cccccc;
    }

    th{
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        font-weight: bold;

    }

    td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;

    }
    
</style>

<body>

    <table>

        <!-- HEADER START  -->

        <tr class="first_row_design">

            <th>{{tr('s_no')}}</th>

            <th >{{tr('username')}}</th>

            <th>{{tr('live_video')}}</th>

            <th>{{tr('payment_id')}}</th>

            <th >{{tr('paid_amount')}}</th>

            <th >{{tr('admin_amount')}}</th>

            <th >{{tr('user_amount')}}</th>

            <th >{{tr('payment_mode')}}</th>

        </tr>

        <!--- HEADER END  -->

        {{$i=0}}

        @foreach($data as $live_video_payments)

            @foreach($live_video_payments as $live_video_payment)

            {{$i=$i+1}}

            <tr>

                <td>{{$i}}</td>

                <td>{{ $live_video_payment->user->name ?? tr('n_a')}}</td>

                <td>{{$live_video_payment->videoDetails->unique_id ?? tr('n_a')}}</td>

                <td>{{$live_video_payment->payment_id ?: tr('n_a')}}</td>

                <td>
                    {{formatted_amount(Setting::get('is_only_wallet_payment') ? $live_video_payment->token : $live_video_payment->amount) ?: tr('n_a')}}
                </td>

                <td >{{$live_video_payment->admin_amount_formatted ?: tr('n_a')}}</td>

                <td >{{$live_video_payment->user_amount_formatted ?: tr('n_a')}}</td>


                <td >{{$live_video_payment->payment_mode ?: tr('n_a')}}</td>

            </tr>

            @endforeach
        
        @endforeach
    </table>

</body>

</html>