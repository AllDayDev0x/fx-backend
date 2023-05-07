<!DOCTYPE html>
<html>

<head>
    <title>{{tr('report')}}</title>
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

            <th>{{tr('total_posts')}}</th>

            <th >{{tr('total_tips')}}</th>

            <th>{{tr('post_payments')}}</th>

            <th>{{tr('subscription_payments')}}</th>

            <th >{{tr('total_likes')}}</th>
             
            <th> {{tr('followers')}} </th>

            <th >{{tr('followings')}}</th>
        </tr>

        <!--- HEADER END  -->

        <tr class="row_col_design">

            <td>{{$data['posts']}}</td>

            <td>{{$data['tip_payment']}}</td>

            <td>{{$data['post_payment']}}</td>

            <td>{{$data['subscription_payment']}}</td>

            <td >{{$data['likes']}}</td>

            <td >{{$data['followers']}}</td>

            <td >{{$data['followings']}}</td>

        </tr>

    </table>

</body>

</html>