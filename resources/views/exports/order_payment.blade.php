<!DOCTYPE html>
<html>

<head>
    <title>{{tr('order_payment')}}</title>
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

            <th>{{ tr('s_no') }}</th>

            <th>{{ tr('username') }}</th>

            <th>{{ tr('order_id')}}</th>

            <th>{{ tr('payment_id') }}</th>

            <th>{{ tr('delivery_price') }}</th>

            <th>{{ tr('sub_total') }}</th>

            <th>{{ tr('total') }}</th>

            <th>{{tr('status')}}</th>

        </tr>

        <!--- HEADER END  -->

        {{$i=0}}

        @foreach($data as $order_payments)

            @foreach($order_payments as $order_payment)

            {{$i=$i+1}}

            <tr>

                <td>{{$i}}</td>

                <td>{{$order_payment->user->name ?? tr('n_a')}}</td>

                <td>{{$order_payment->unique_id ?: tr('n_a')}}</td>

                <td>{{$order_payment->payment_id ?: tr('n_a')}}</td>

                <td>
                    {{$order_payment->delivery_price_formatted ?: tr('n_a')}}
                </td>

                <td >{{$order_payment->sub_total_formatted ?: tr('n_a')}}</td>

                <td >{{$order_payment->total_formatted ?: tr('n_a')}}</td>


                <td >
                    @if($order_payment->status == APPROVED)

                        {{ tr('approved') }}

                    @else

                        {{ tr('declined') }}

                    @endif
                </td>

            </tr>

            @endforeach
        
        @endforeach
    </table>

</body>

</html>