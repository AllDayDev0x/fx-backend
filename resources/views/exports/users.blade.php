<!DOCTYPE html>
<html>

<head>
    <title>{{tr('users_management')}}</title>
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

            <th>{{tr('email')}}</th>

            <th>{{tr('mobile')}}</th>

            <th >{{tr('picture')}}</th>

            <th >{{tr('address')}}</th>

            <th >{{tr('user_account_type')}}</th>

            <th >{{tr('payment_mode')}}</th>

            <th >{{tr('device_type')}}</th>

            <th>{{ tr('wallet_balance') }}</th>

            <th >{{tr('status')}}</th>

            <th >{{tr('created')}}</th>

            <th >{{tr('updated')}}</th>
        </tr>

        <!--- HEADER END  -->

        @foreach($data as $i => $user)

        <tr @if($i % 2 == 0) class="row_col_design" @endif >

            <td>{{$i+1}}</td>

            <td>{{$user->name ?: tr('n_a')}}</td>

            <td>{{$user->email ?: tr('n_a')}}</td>

            <td>{{$user->mobile ?: tr('n_a')}}</td>

            <td>
                @if($user->picture) {{$user->picture}} @else {{asset('admin-css/dist/img/avatar.png')}} @endif
            </td>

            <td >{{$user->address ?: tr('n_a')}}</td>

            <td>

                @if($user->user_account_type == USER_PREMIUM_ACCOUNT)
                                                
                    {{ tr('USER_PREMIUM_ACCOUNT') }}

                @else

                    {{ tr('USER_FREE_ACCOUNT') }}

                @endif

            </td>

            <td >{{$user->payment_mode ?: tr('n_a')}}</td>

            <td >{{$user->device_type ?: tr('n_a')}}</td>

            <td> {{$user->userWallets->remaining_formatted ?? formatted_amount(0.00)}}</td>

            <td >{{common_date($user->expiry_date, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

            @if($user->status == USER_APPROVED) 
            <td >{{tr('approved')}}</td>

            @else
            <td >{{tr('declined')}}</td>

            @endif

            <td>{{common_date($user->created_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

            <td>{{common_date($user->updated_at, Auth::guard('admin')->user()->timezone, 'd-m-Y H:i a')}}</td>

        </tr>

        @endforeach
    </table>

</body>

</html>