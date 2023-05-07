<!DOCTYPE html>
<html>

<head>
    <title>{{tr('post_payment')}}</title>
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

            <th>{{tr('post_id')}}</th>

            <th >{{tr('tip_amount')}}</th>

            <th >{{tr('admin_amount')}}</th>

            <th >{{tr('user_amount')}}</th>

            <th >{{tr('status')}}</th>

        </tr>

        <!--- HEADER END  -->

        {{$i=0}}

        @foreach($data as $user_tips)

            @foreach($user_tips as $tips)

            {{$i=$i+1}}

            <tr>

                <td>{{$i}}</td>

                <td>{{$tips->from_username ?: tr('n_a')}}</td>

                <td>{{$tips->to_username ?: tr('n_a')}}</td>

                <td>
                    @if($tips->post && $tips->post->unique_id)
                    {{ $tips->post->unique_id}}
                    @else
                    {{tr('not_available') }}
                    @endif
                </td>

                <td>
                    {{$tips->amount_formatted ?: tr('n_a')}}
                </td>

                <td >{{$tips->admin_amount_formatted ?: tr('n_a')}}</td>

                <td >{{$tips->user_amount_formatted ?: tr('n_a')}}</td>


                <td >
                    @if($tips->status == APPROVED)

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