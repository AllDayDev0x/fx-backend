<!DOCTYPE html>
<html>

<head>
    <title>{{tr('audio_call_payments')}}</title>
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

            <th>{{tr('payment_id')}}</th>

            <th>{{tr('user')}}</th>

            <th >{{tr('model')}}</th>

            <th >{{tr('amount')}}</th>

            <th >{{tr('admin_amount')}}</th>

            <th >{{tr('user_amount')}}</th>

            <th >{{tr('status')}}</th>

        </tr>

        <!--- HEADER END  -->
        {{$i=0}}

        @foreach($data as $audio_call_payments)

            @foreach($audio_call_payments as $audio_call_payment)

            {{$i=$i+1}}

            <tr>

                <td>{{$i}}</td>

                <td>{{$audio_call_payment->payment_id ?: tr('n_a')}}</td>

                <td>{{$audio_call_payment->user->name ?? tr('n_a')}}</td>

                <td>{{$audio_call_payment->model->name ?? tr('n_a')}}</td>

                <td>{{$audio_call_payment->paid_amount_formatted ?: tr('n_a')}}</td>

                <td>{{$audio_call_payment->admin_amount_formatted ?: tr('n_a')}}</td>

                <td>{{$audio_call_payment->user_amount_formatted ?: tr('n_a')}}</td>

                <td>
                    @if($audio_call_payment->status == PAID)

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