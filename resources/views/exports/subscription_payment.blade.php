<!DOCTYPE html>
<html>

<head>
    <title>{{tr('subscription_payment')}}</title>
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

            <th >{{tr('from_username')}}</th>

            <th>{{tr('to_username')}}</th>

            <th>{{tr('payment_id')}}</th>

            <th>{{tr('plan')}}</th>

            <th >{{tr('subscription_amount')}}</th>

            <th >{{tr('admin_amount')}}</th>

            <th >{{tr('user_amount')}}</th>

            <th >{{tr('status')}}</th>

        </tr>

        <!--- HEADER END  -->
        {{$i=0}}

        @foreach($data as $subscriptions)

            @foreach($subscriptions as $subscription)

            {{$i=$i+1}}

            <tr>

                <td>{{$i}}</td>

                <td>{{$subscription->from_username ?: tr('n_a')}}</td>

                <td>{{$subscription->to_username ?: tr('n_a')}}</td>

                <td>{{$subscription->payment_id ?: tr('n_a')}}</td>

                <td>
                    {{ substr($subscription->plan_text_formatted, 0, -1) ?? tr('n_a') }}
                </td>

                <td >{{$subscription->amount_formatted ?: tr('n_a')}}</td>

                <td >{{$subscription->admin_amount_formatted ?: tr('n_a')}}</td>

                <td >{{$subscription->user_amount_formatted ?: tr('n_a')}}</td>


                <td >
                    @if($subscription->status == APPROVED)

                    {{ tr('paid') }}

                    @else

                    {{ tr('not_paid') }}

                    @endif
                </td>

            </tr>

            @endforeach

        @endforeach
    </table>

</body>

</html>