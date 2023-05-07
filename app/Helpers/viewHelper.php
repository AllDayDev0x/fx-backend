<?php

use Carbon\Carbon;

// Helper, Setting, Log;

use App\Models\User, App\Models\PageCounter, App\Models\Settings;

use App\Models\SubscriptionPayment, App\Models\PromoCode, App\Models\UserPromoCode;

use App\Repositories\CommonRepository as CommonRepo;

function amount_convertion($percentage, $amt) {

    $converted_amt = $amt * ($percentage/100);

    return $converted_amt;
}

/**
 * @method promo_calculation()
 *
 * @uses Calculate price based on total days
 *
 * @created Subham
 *
 * @updated
 *
 * @param date $start_date
 *
 * @return string $amount
 */
function promo_calculation($amount,$request) {

    if ($request->promo_code) {

        $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();

        $user_details = User::where('id', $request->id)->first();

        $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details, $promo_code)->getData();

        if($check_promo_code->success == true){

            $amount_convertion = $promo_code->amount;

            if ($promo_code->amount_type == PERCENTAGE) {

                $amount_convertion = amount_convertion($promo_code->amount, $amount);

            }

            if ($amount_convertion < $amount) {

                $total_amount = $amount - $amount_convertion;

                $coupon_amount = $amount_convertion;

            } else {

                // If the coupon amount greater than subscription amount, then assign to zero.

                $total_amount = 0;

                $coupon_amount = $amount_convertion;
                
            }

            $amount = $coupon_amount;

        }

        $user_promo_code = UserPromoCode::where('user_id', $request->id)->where('promo_code', $request->promo_code)->first() ?? new UserPromoCode;

        $user_promo_code->user_id = $request->id;

        $user_promo_code->promo_code = $request->promo_code;

        $user_promo_code->no_of_times_used += 1;

        $user_promo_code->save();

    }
    
    return $coupon_amount;

}

/**
 * @method tr()
 *
 * Description: used to convert the string to language based string
 *
 * @created Vidhya R
 *
 * @updated
 *
 * @param string $key
 *
 * @return string value
 */
function tr($key , $additional_key = "" , $lang_path = "messages.") {

    // if(Auth::guard('admin')->check()) {

    //     $locale = config('app.locale');

    // } else {

        if (!\Session::has('locale')) {

            $locale = \Session::put('locale', config('app.locale'));

        }else {

            $locale = \Session::get('locale');

        }
    // }
    return \Lang::choice('messages.'.$key, 0, Array('other_key' => $additional_key), $locale);

}

function api_success($key , $other_key = "" , $lang_path = "messages.") {

    if (!\Session::has('locale')) {

        $locale = \Session::put('locale', config('app.locale'));

    } else {

        $locale = \Session::get('locale');

    }
    return \Lang::choice('api-success.'.$key, 0, Array('other_key' => $other_key), $locale);

}

function api_error($key , $other_key = "" , $lang_path = "messages.") {

    if (!\Session::has('locale')) {

        $locale = \Session::put('locale', config('app.locale'));

    } else {

        $locale = \Session::get('locale');

    }
    return \Lang::choice('api-error.'.$key, 0, Array('other_key' => $other_key), $locale);

}

/**
 * @method envfile()
 *
 * Description: get the configuration value from .env file 
 *
 * @created Vidhya R
 *
 * @updated
 *
 * @param string $key
 *
 * @return string value
 */

function envfile($key) {

    $data = getEnvValues();

    if($data) {
        return $data[$key];
    }

    return "";

}

function getEnvValues() {

    $data =  [];

    $path = base_path('.env');

    if(file_exists($path)) {

        $values = file_get_contents($path);

        $values = explode("\n", $values);

        foreach ($values as $key => $value) {

            $var = explode('=',$value);

            if(count($var) == 2 ) {
                if($var[0] != "")
                    $data[$var[0]] = $var[1] ? $var[1] : null;
            } else if(count($var) > 2 ) {
                $keyvalue = "";
                foreach ($var as $i => $imp) {
                    if ($i != 0) {
                        $keyvalue = ($keyvalue) ? $keyvalue.'='.$imp : $imp;
                    }
                }
                $data[$var[0]] = $var[1] ? $keyvalue : null;
            }else {
                if($var[0] != "")
                    $data[$var[0]] = null;
            }
        }

        array_filter($data);
    
    }

    return $data;

}

/**
 * @method register_mobile()
 *
 * Description: Update the user register device details 
 *
 * @created Vidhya R
 *
 * @updated
 *
 * @param string $device_type
 *
 * @return - 
 */

function register_mobile($device_type) {

    // if($reg = MobileRegister::where('type' , $device_type)->first()) {

    //     $reg->count = $reg->count + 1;

    //     $reg->save();
    // }
    
}

/**
 * Function Name : subtract_count()
 *
 * Description: While Delete user, subtract the count from mobile register table based on the device type
 *
 * @created vithya R
 *
 * @updated vithya R
 *
 * @param string $device_ype : Device Type (Andriod,web or IOS)
 * 
 * @return boolean
 */

function subtract_count($device_type) {

    if($reg = MobileRegister::where('type' , $device_type)->first()) {

        $reg->count = $reg->count - 1;
        
        $reg->save();
    }

}

/**
 * @method get_register_count()
 *
 * Description: Get no of register counts based on the devices (web, android and iOS)
 *
 * @created Vidhya R
 *
 * @updated
 *
 * @param - 
 *
 * @return array value
 */

function get_register_count() {

    $ios_count = MobileRegister::where('type' , 'ios')->get()->count();

    $android_count = MobileRegister::where('type' , 'android')->get()->count();

    $web_count = MobileRegister::where('type' , 'web')->get()->count();

    $total = $ios_count + $android_count + $web_count;

    return array('total' => $total , 'ios' => $ios_count , 'android' => $android_count , 'web' => $web_count);

}

/**
 * @method: last_x_days_page_view()
 *
 * @uses: to get last x days page visitors analytics
 *
 * @created Anjana H
 *
 * @updated Anjana H
 *
 * @param - 
 *
 * @return array value
 */
function last_x_days_page_view($days){

    $views = PageCounter::orderBy('created_at','asc')->where('created_at', '>', Carbon::now()->subDays($days))->where('page','home');
 
    $arr = array();
 
    $arr['count'] = $views->count();

    $arr['get'] = $views->get();

      return $arr;
}

function counter($page = 'home'){

    $count_home = PageCounter::wherePage($page)->where('created_at', '>=', new DateTime('today'));

        if($count_home->count() > 0) {
            $update_count = $count_home->first();
            $update_count->count = $update_count->count + 1;
            $update_count->save();
        } else {
            $create_count = new PageCounter;
            $create_count->page = $page;
            $create_count->count = 1;
            $create_count->save();
        }

}

//this function convert string to UTC time zone

function convertTimeToUTCzone($str, $userTimezone, $format = 'Y-m-d H:i:s') {

    $new_str = new DateTime($str, new DateTimeZone($userTimezone));

    $new_str->setTimeZone(new DateTimeZone('UTC'));

    return $new_str->format( $format);
}

//this function converts string from UTC time zone to current user timezone

function convertTimeToUSERzone($str, $userTimezone, $format = 'Y-m-d H:i:s') {

    if(empty($str)){
        return '';
    }
    
    try {
        
        $new_str = new DateTime($str, new DateTimeZone('UTC') );
        
        $new_str->setTimeZone(new DateTimeZone( $userTimezone ));
    }
    catch(\Exception $e) {
        // Do Nothing
    }
    
    return $new_str->format( $format);
}

function number_format_short( $n, $precision = 1 ) {

    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }
    return $n_format . $suffix;

}

function common_date($date , $timezone , $format = "d M Y h:i A") {

    if(!$date) {

        return "";

    }
    
    if($timezone) {

        $date = convertTimeToUSERzone($date , $timezone , $format);

    }   
   
    return date($format , strtotime($date));
}


/**
 * function delete_value_prefix()
 * 
 * @uses used for concat string, while deleting the records from the table
 *
 * @created vidhya R
 *
 * @updated vidhya R
 *
 * @param $prefix - from settings table (Setting::get('prefix_user_delete'))
 *
 * @param $primary_id - Primary ID of the delete record
 *
 * @param $is_email 
 *
 * @return concat string based on the input values
 */

function delete_value_prefix($prefix , $primary_id , $is_email = 0) {

    if($is_email) {

        $site_name = str_replace(' ', '_', Setting::get('site_name'));

        return $prefix.$primary_id."@".$site_name.".com";
        
    } else {
        return $prefix.$primary_id;

    }

}

/**
 * function routefreestring()
 * 
 * @uses used for remove the route parameters from the string
 *
 * @created vidhya R
 *
 * @updated vidhya R
 *
 * @param string $string
 *
 * @return Route parameters free string
 */

function routefreestring($string) {

    $string = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $string));
    
    $search = [' ', '&', '%', "?",'=','{','}','$'];

    $replace = ['-', '-', '-' , '-', '-', '-' , '-','-'];

    $string = str_replace($search, $replace, $string);

    return $string;
    
}

/**
 * @method selected()
 *
 * @uses set selected item 
 *
 * @created Anjana H
 *
 * @updated Anjana H
 *
 * @param $array, $id, $check_key_name
 *
 * @return response of array 
 */
function selected($array, $id, $check_key_name) {
    
    $is_key_array = is_array($id);
    
    foreach ($array as $key => $value) {

        $value->is_selected = ($value->$check_key_name == $id) ? YES : NO;
    }  

    return $array;
}


function nFormatter($num, $currency = "") {

    $currency = Setting::get('currency', "$");

    if($num>1000) {

        $x = round($num);

        $x_number_format = number_format($x);

        $x_array = explode(',', $x_number_format);

        $x_parts = ['k', 'm', 'b', 't'];

        $x_count_parts = count($x_array) - 1;

        $x_display = $x;

        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');

        $x_display .= $x_parts[$x_count_parts - 1];

        return $currency." ".$x_display;

    }

    return $currency." ".$num;

}

/**
 * @method formatted_plan()
 *
 * @uses used to format the number
 *
 * @created Bhawya
 *
 * @updated Akshata
 *
 * @param integer $num
 * 
 * @param string $currency
 *
 * @return string $formatted_plan
 */

function formatted_plan($plan = 0, $type = "month") {

    switch ($type) {

        case 'weeks':

            $text = $plan <= 1 ? tr('week') : tr('weeks');

            break;

        case 'days':

            $text = $plan <= 1 ? tr('day') : tr('days');

            break;

        case 'years':

            $text = $plan <= 1 ? tr('year') : tr('years');

            break;
        
        default:
        
            $text = $plan <= 1 ? tr('month') : tr('months');
            
            break;
    }
    
    return $plan." ".$text;
}

/**
 * @method formatted_amount()
 *
 * @uses used to format the number
 *
 * @created vidhya R
 *
 * @updated vidhya R
 *
 * @param integer $num
 * 
 * @param string $currency
 *
 * @return string $formatted_amount
 */

function formatted_amount($amount = 0.00, $currency = "", $symbol = "",$is_formatted = YES) {
   
    if(Setting::get('is_only_wallet_payment') && $is_formatted == YES) {

        $symbol = $symbol ?: Setting::get('token_symbol', 'TOK');

        $amount = number_format((float)$amount, 2, '.', '');

        $formatted_token = Setting::get('symbol_position') == PREFIX ? $symbol." ".($amount ?: "0.00") : ($amount ?: "0.00")." ".$symbol;

        return $formatted_token;

    } else {

        $currency = $currency ?: Setting::get('currency', '$');

        $amount = number_format((float)$amount, 2, '.', '');

        $formatted_amount = $currency."".$amount ?: "0.00";

        $formatted_token = Setting::get('symbol_position') == PREFIX ? $currency." ".($amount ?: "0.00") : ($amount ?: "0.00")." ".$currency;

        return $formatted_amount;

    }
}

function readFileLength($file)  {

    $variableLength = 0;
    if (($handle = fopen($file, "r")) !== FALSE) {
         $row = 1;
         while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) {
            $num = count($data);
            $row++;
            for ($c=0; $c < $num; $c++) {
                $exp = explode("=>", $data[$c]);
                if (count($exp) == 2) {
                    $variableLength += 1; 
                }
            }
        }
        fclose($handle);
    }

    return $variableLength;
}

function no_of_class_formatted($no_of_class = 1) {

    $no_of_class = $no_of_class <= 1 ? $no_of_class.' '.tr('class') : $no_of_class.' '.tr('class').'s';

    return $no_of_class;

}

function no_of_users_each_class_formatted($no_of_users) {

    $no_of_users = $no_of_users <= 1 ? $no_of_users.' '.tr('no_of_user') : $no_of_users.' '.tr('no_of_user').'s';

    return $no_of_users."/per class";

}

function getExpiryDate($plan,$plan_type) {
   
    $expiry_date = date('Y-m-d h:i:s',strtotime("+{$plan}{$plan_type}"));

    return $expiry_date;
}

function last_6_months_data() {

    $months = 6;

    $data = new \stdClass;

    $data->currency = $currency = Setting::get('currency', '$');

    $last_x_days_revenues = [];

    $start  = new \DateTime('-6 month', new \DateTimeZone('UTC'));
    
    $period = new \DatePeriod($start, new \DateInterval('P1M'), $months);
    
    $dates = $last_x_days_revenues = [];

    foreach ($period as $date) {

        $current_month = $date->format('M');

        $last_x_days_data = new \stdClass;

        $last_x_days_data->month= $current_month;

        $month = $date->format('m');
      
        $last_x_days_total_earnings = SubscriptionPayment::whereMonth('paid_date', '=', $month)->where('status' , PAID)->sum('amount');
        
        $last_x_days_data->total_earnings = $last_x_days_total_earnings ?: 0.00;

        array_push($last_x_days_revenues, $last_x_days_data);

    }
    
    $data->last_x_days_revenues = $last_x_days_revenues;
    
    return $data;  
}

function static_page_footers($section_type = 0, $is_list = NO) {

    $lists = [
                STATIC_PAGE_SECTION_1 => tr('STATIC_PAGE_SECTION_1')."(".Setting::get('site_name').")",
                STATIC_PAGE_SECTION_2 => tr('STATIC_PAGE_SECTION_2')."(
                Discover)",
                //STATIC_PAGE_SECTION_3 => tr('STATIC_PAGE_SECTION_3')."(Hosting)",
                //STATIC_PAGE_SECTION_4 => tr('STATIC_PAGE_SECTION_4')."(Social)",
            ];

    if($is_list == YES) {
        return $lists;
    }

    return isset($lists[$section_type]) ? $lists[$section_type] : "Common";

}


function last_x_months_data($months) {

    $data = new \stdClass;

    $data->currency = $currency = Setting::get('currency', '$');

    $last_x_days_revenues = [];

    $start  = new \DateTime('-6 month', new \DateTimeZone('UTC'));
    
    $period = new \DatePeriod($start, new \DateInterval('P1M'), $months);
   
    $dates = $last_x_days_revenues = [];

    foreach ($period as $date) {

        $current_month = $date->format('M');

        $formatted_month = $date->format('M Y');

        $last_x_days_data =  new \stdClass;

        $last_x_days_data->month= $current_month;

        $last_x_days_data->formatted_month = $formatted_month;

        $month = $date->format('m');
      
        $last_x_days_subscription_earnings = \App\Models\SubscriptionPayment::whereMonth('paid_date', '=', $month)->where('status' , PAID)->sum('amount');

        $last_x_days_order_earnings = \App\Models\OrderPayment::whereMonth('paid_date', '=', $month)->where('status' , PAID)->sum('total');

        $last_x_days_post_earnings = \App\Models\PostPayment::whereMonth('paid_date', '=', $month)->where('status' , PAID)->sum('paid_amount');
        
        $last_x_days_data->subscription_earnings = $last_x_days_subscription_earnings ?: 0.00;

        $last_x_days_data->order_earnings = $last_x_days_order_earnings ?: 0.00;

        $last_x_days_data->post_earnings = $last_x_days_post_earnings ?: 0.00;

        array_push($last_x_days_revenues, $last_x_days_data);

    }
    
    $data->last_x_days_revenues = $last_x_days_revenues;
    
    return $data;  
}

function last_x_days_revenue($days,$order_products_ids) {
            
    $data = new \stdClass;

    $data->currency = $currency = Setting::get('currency', '$');

    // Last 10 days revenues

    $last_x_days_revenues = [];

    $start  = new \DateTime('-7 day', new \DateTimeZone('UTC'));
    
    $period = new \DatePeriod($start, new \DateInterval('P1D'), $days);
   
    $dates = $last_x_days_revenues = [];

    foreach ($period as $date) {

        $current_date = $date->format('Y-m-d');

        $last_x_days_data = new \stdClass;

        $last_x_days_data->date = $current_date;
      
        $last_x_days_total_earnings = \App\Models\OrderPayment::whereIn('order_id',$order_products_ids)->whereDate('paid_date', $current_date)->sum(Setting::get('is_only_wallet_payment') ? 'token' : 'total');
      
        $last_x_days_data->total_earnings = $last_x_days_total_earnings ?: 0.00;

        array_push($last_x_days_revenues, $last_x_days_data);

    }
    
    $data->last_x_days_revenues = $last_x_days_revenues;
    
    return $data;   

}

/**
 * @method revenue_graph()
 *
 * @uses to get revenue analytics 
 *
 * @created Akshata
 * 
 * @updated Akshata
 * 
 * @param  integer $days
 * 
 * @return array of revenue totals
 */
function revenue_graph($days) {
            
    $data = new \stdClass;

    $data->currency = $currency = Setting::get('currency', '$');

    // Last 10 days revenues

    $last_x_days_revenues = [];

    $start  = new \DateTime('-6 day', new \DateTimeZone('UTC'));
    
    $period = new \DatePeriod($start, new \DateInterval('P1D'), $days);
   
    $dates = $last_x_days_revenues = [];

    foreach ($period as $date) {

        $current_date = $date->format('Y-m-d');

        $last_x_days_data = new \stdClass;

        $last_x_days_data->date = $current_date;
      
        $last_x_days_post_total_earnings = \App\Models\PostPayment::where('status',PAID)->whereDate('paid_date', '=', $current_date)->sum((Setting::get('is_only_wallet_payment') ? 'token' : 'paid_amount'));

        $last_x_days_subscription_total_earnings = \App\Models\SubscriptionPayment::where('status',PAID)->whereDate('paid_date', '=', $current_date)->sum('amount');
      
        $last_x_days_data->total_post_earnings = $last_x_days_post_total_earnings ?: 0.00;

        $last_x_days_data->total_subscription_earnings = $last_x_days_subscription_total_earnings ?: 0.00;

        array_push($last_x_days_revenues, $last_x_days_data);

    }
    
    $data->last_x_days_revenues = $last_x_days_revenues;
    
    return $data;   

}
/**
 * @method get_wallet_message()
 *
 * @uses used to get the wallet message based on the types
 * 
 * @created vidhya R
 *
 * @updated vidhya R
 * 
 */

function get_wallet_message($user_wallet_payment) {

    $amount_type = $user_wallet_payment->payment_type;

    $status_text = [
        WALLET_PAYMENT_TYPE_ADD => tr('WALLET_PAYMENT_TYPE_ADD_TEXT'),WALLET_PAYMENT_TYPE_PAID => tr('WALLET_PAYMENT_TYPE_PAID_TEXT'), WALLET_PAYMENT_TYPE_CREDIT => tr('WALLET_PAYMENT_TYPE_CREDIT_TEXT'), WALLET_PAYMENT_TYPE_WITHDRAWAL => tr('WALLET_PAYMENT_TYPE_WITHDRAWAL_TEXT')];

    return isset($status_text[$amount_type]) ? $status_text[$amount_type] : tr('WALLET_PAYMENT_TYPE_ADD_TEXT');

}

function wallet_formatted_amount($amount = 0.00, $amount_type = WALLET_AMOUNT_TYPE_ADD) {

    $amount_symbol = $amount_type == WALLET_AMOUNT_TYPE_ADD ? "+" : "-";

    return $amount_symbol." ".formatted_amount($amount);

}

function paid_status_formatted($status,$payment_type = '') {

    $status_list = [
        USER_WALLET_PAYMENT_INITIALIZE => tr('USER_WALLET_PAYMENT_INITIALIZE'), 
        USER_WALLET_PAYMENT_PAID => $payment_type == WALLET_PAYMENT_TYPE_WITHDRAWAL ? tr('WITHDRAW_INITIATED'): tr('USER_WALLET_PAYMENT_PAID'), 
        USER_WALLET_PAYMENT_UNPAID => tr('USER_WALLET_PAYMENT_UNPAID'), 
        USER_WALLET_PAYMENT_CANCELLED => tr('USER_WALLET_PAYMENT_CANCELLED'),
        USER_WALLET_PAYMENT_DISPUTED => tr('USER_WALLET_PAYMENT_DISPUTED'),
        USER_WALLET_PAYMENT_WAITING => tr('USER_WALLET_PAYMENT_WAITING')
    ];

    return isset($status_list[$status]) ? $status_list[$status] : tr('paid');
}

function wallet_picture($amount_type = WALLET_AMOUNT_TYPE_ADD) {

    $wallet_picture = $amount_type == WALLET_AMOUNT_TYPE_ADD ? asset('images/wallet_plus.png') : asset('images/wallet_minus.png');

    return $wallet_picture;

}

function total_days($end_date, $start_date = "") {

    $start_date = $start_date ?? date('Y-m-d H:i:s');

    $start_date = strtotime($start_date);

    $end_date = strtotime($end_date);

    $datediff = $start_date - $end_date;

    return round($datediff / (60 * 60 * 24));
}


/**
 * @param $image_path
 * @return bool|mixed
 */
function get_image_mime_type($image_path)
{
    $mimes  = array(
        IMAGETYPE_GIF => "image/gif",
        IMAGETYPE_JPEG => "image/jpg",
        IMAGETYPE_PNG => "image/png",
        IMAGETYPE_SWF => "image/swf",
        IMAGETYPE_PSD => "image/psd",
        IMAGETYPE_BMP => "image/bmp",
        IMAGETYPE_TIFF_II => "image/tiff",
        IMAGETYPE_TIFF_MM => "image/tiff",
        IMAGETYPE_JPC => "image/jpc",
        IMAGETYPE_JP2 => "image/jp2",
        IMAGETYPE_JPX => "image/jpx",
        IMAGETYPE_JB2 => "image/jb2",
        IMAGETYPE_SWC => "image/swc",
        IMAGETYPE_IFF => "image/iff",
        IMAGETYPE_WBMP => "image/wbmp",
        IMAGETYPE_XBM => "image/xbm",
        IMAGETYPE_ICO => "image/ico");

    if (($image_type = exif_imagetype($image_path))
        && (array_key_exists($image_type ,$mimes)))
    {
        return $mimes[$image_type];
    }
    else
    {
        return FALSE;
    }
}

function get_post_file_type($file_url) {
    
    return 'image';

}

function user_document_status_formatted($status) {

    $status_list = [
                USER_DOCUMENT_NONE => tr('USER_DOCUMENT_NONE'),
                USER_DOCUMENT_PENDING => tr('USER_DOCUMENT_PENDING'),
                USER_DOCUMENT_APPROVED => tr('USER_DOCUMENT_APPROVED'),
                USER_DOCUMENT_DECLINED => tr('USER_DOCUMENT_DECLINED')
                ];

    return isset($status_list[$status]) ? $status_list[$status] : tr('USER_DOCUMENT_NONE');
}

function get_follower_ids($user_id) {

    $follower_ids = \App\Models\Follower::where('follower_id', $user_id)->where('status', YES)->pluck('user_id');

    $follower_ids = $follower_ids ? $follower_ids->toArray() : [];

    return $follower_ids;
}

function get_post_temp_path($user_id, $url) {

    $filename = basename($url);

    $folder_path = POST_TEMP_PATH.$user_id.'/';

    return 'public/'.$folder_path.$filename;

}

function get_post_path($user_id, $url) {

    $filename = basename($url);

    $folder_path = POST_PATH.$user_id.'/';

    return 'public/'.$folder_path.$filename;

}


function push_messages($key , $other_key = "" , $lang_path = "messages.") {


    if (!\Session::has('locale')) {

        $locale = \Session::put('locale', config('app.locale'));

    }else {

        $locale = \Session::get('locale');

    }

  return \Lang::choice('push-messages.'.$key, 0, Array('other_key' => $other_key), $locale);

}


function user_account_type_formatted($type) {

    $list = [USER_FREE_ACCOUNT => tr('USER_FREE_ACCOUNT'), USER_PREMIUM_ACCOUNT => tr('USER_PREMIUM_ACCOUNT')];

    return $list[$type] ?? tr('USER_FREE_ACCOUNT');

}

function withdraw_picture($amount_type = WALLET_AMOUNT_TYPE_ADD) {

    $withdraw_picture = asset('images/withdraw_sent.png');

    return $withdraw_picture;

}


function withdrawal_status_formatted($status) {

    $status_list = [WITHDRAW_INITIATED => tr('WITHDRAW_INITIATED'), WITHDRAW_PAID => tr('WITHDRAW_PAID'), WITHDRAW_ONHOLD => tr('WITHDRAW_ONHOLD'), WITHDRAW_DECLINED => tr('WITHDRAW_DECLINED'), WITHDRAW_CANCELLED => tr('WITHDRAW_CANCELLED')];

    return isset($status_list[$status]) ? $status_list[$status] : tr('WITHDRAW_INITIATED');
}

function document_status_formatted($status) {

    $status_list = [USER_DOCUMENT_NONE => tr('USER_DOCUMENT_NONE'), USER_DOCUMENT_PENDING => tr('USER_DOCUMENT_PENDING'), USER_DOCUMENT_APPROVED => tr('USER_DOCUMENT_APPROVED'), USER_DOCUMENT_DECLINED => tr('USER_DOCUMENT_DECLINED')];

    return isset($status_list[$status]) ? $status_list[$status] : tr('USER_KYC_DOCUMENT_NONE');
}

function document_status_text_formatted($status) {

    $status_list = [
        USER_DOCUMENT_NONE => tr('user_document_none'), 
        USER_DOCUMENT_PENDING => tr('user_document_veification_pending'),
        USER_DOCUMENT_APPROVED => tr('user_document_approved_text'), 
        USER_DOCUMENT_DECLINED => tr('user_document_declined')
    ];

    return isset($status_list[$status]) ? $status_list[$status] : tr('USER_KYC_DOCUMENT_NONE');
}

/**
 * @method last_x_months_posts()
 *
 * @uses used to get no.of.posts for the month
 * 
 * @created Ganesh
 *
 * @updated Subham
 * 
 */


function last_x_months_posts($months,$user_id='') {

    $data = new \stdClass;

    $start  = new \DateTime('-8 month', new \DateTimeZone('UTC'));
    
    $period = new \DatePeriod($start, new \DateInterval('P1M'), $months);
   
    $dates = $last_x_months_posts = [];

    foreach ($period as $date) {

        $formatted_month = $date->format('M Y');

        $last_x_months_posts_data =  new \stdClass;

        $base_query = \App\Models\Post::whereYear('created_at',$date->format('Y'))->whereMonth('created_at', '=', $date->format('m'));

        if($user_id){

            $base_query = $base_query->where('user_id',$user_id);
        }

        $last_x_months_posts_data->no_of_posts = $base_query->count();

        $last_x_months_posts_data->month = $formatted_month;

        array_push($last_x_months_posts, $last_x_months_posts_data);

    }
    

    return $last_x_months_posts;   

}


function plan_text($plan, $plan_type = PLAN_TYPE_MONTH) {
    
    $plan_type_text = $plan <= 1 ? tr($plan_type) : tr($plan_type)."s";
    
   return  $plan_text = $plan." ".$plan_type_text;

}

function generate_payment_id() {

    $payment_id = time();

    $payment_id .= rand();

    $payment_id = sha1($payment_id);

    return strtoupper($payment_id);

}

/**
 * @method blocked_users()
 *
 * @uses used to get the blocked users 
 * 
 * @created Ganesh
 *
 * @updated Ganesh
 * 
 */
function blocked_users($user_id){

    $block_user_ids = \App\Models\BlockUser::where('block_by',$user_id)->pluck('blocked_to')->toArray() ?? [];

    return $block_user_ids;
}

/**
 * @method blocked_user_status()
 *
 * @uses used to get the blocked users 
 * 
 * @created Ganesh
 *
 * @updated Ganesh
 * 
 */
function blocked_user_status($loggedin_user_id, $user_id){

    $block_user = \App\Models\BlockUser::where('block_by', $loggedin_user_id)->where('blocked_to', $user_id)->count() ?? 0;

    return $block_user;
}


/**
 * @method report_posts()
 *
 * @uses used to get the reported posts
 * 
 * @created Ganesh
 *
 * @updated Ganesh
 * 
 */

function report_posts($user_id){

    $report_post_ids = \App\Models\ReportPost::where('block_by',$user_id)->pluck('post_id')->toArray() ?? [];

    return $report_post_ids;
}

function admin_commission_spilit($total) {

    $admin_commission = Setting::get('admin_commission', 1)/100;

    $admin_amount = $total * $admin_commission;

    $user_amount = $total - $admin_amount;

    return  (object) ['admin_amount' => $admin_amount, 'user_amount' => $user_amount];

}


/**
 * @method check_user_subscribed()
 *
 * @uses check the user subscribed
 * 
 * @created Ganesh
 *
 * @updated Ganesh
 * 
 */
function check_user_subscribed($post_user,$request) {

    $current_date = Carbon::now()->format('Y-m-d');
    
    $user_subscription = \App\Models\UserSubscription::where('user_id', $post_user->id)->first();

    $is_subscribed = NO;

    if($user_subscription) {

        $is_subscribed = \App\Models\UserSubscriptionPayment::where('is_current_subscription',YES)->whereDate('expiry_date','>=',$current_date)->where('user_subscription_id', $user_subscription->id)->where('from_user_id', $request->id)->where('to_user_id', $post_user->id)->count() ?? 0;
    }

    return $is_subscribed > 0 ? YES :NO;
    
}

function emptyObject() {
    return (Object)[];
}


function get_file_type($file) {

    $imagemimes = ['image/png', 'image/jpeg', 'image/jpg']; //Add more mimes that you want to support
    
    $videomimes = ['video/mp4', 'video/mov', 'video/webm', 'video/flv', 'video/avi', 'video/mkv']; //Add more mimes that you want to support
    
    $audiomimes = ['audio/mpeg', 'audio/mp3', 'audio/x-wav']; //Add more mimes that you want to support

    if(in_array($file->getMimeType() ,$imagemimes)) {
        return FILE_TYPE_IMAGE;
    }
    //Validate video
    if (in_array($file->getMimeType() ,$videomimes)) {
        return FILE_TYPE_VIDEO;
    }
    //validate audio
    if (in_array($file->getMimeType() ,$audiomimes)) {
        return FILE_TYPE_AUDIO;
    }

    return FILE_TYPE_IMAGE;   
}

function formatUrl($url) {

    $parsed = parse_url($url);

    if(empty($parsed['scheme'])) {

        if(false === strpos($url, '://')) {

            $url = 'https://' . ltrim($url, '/');

        } else {

            $url = 'https' . ltrim($url, '/');
        }
    }

    return $url;

    Log::info("url".$url);
}


/**
 * @method call_status_formatted()
 *
 * @uses used to get call status
 * 
 * @created Ganesh
 *
 * @updated Ganesh
 * 
 */
function call_status_formatted($status, $is_model = YES , $payment_status = NO, $is_paid = NO) {

    $status_list = [
                VIDEO_CALL_REQUEST_SENT => $is_model ? tr('VIDEO_CALL_REQUEST_RECEIVED'): tr('VIDEO_CALL_REQUEST_SENT'),
                VIDEO_CALL_REQUEST_ACCEPTED => $payment_status == YES ? ($is_paid ? tr('VIDEO_CALL_REQUEST_ACCEPTED_PAYMENT') : tr('VIDEO_CALL_REQUEST_ACCEPTED_WAITING')) : tr('VIDEO_CALL_REQUEST_ACCEPTED'),
                VIDEO_CALL_REQUEST_REJECTED => tr('VIDEO_CALL_REQUEST_REJECTED'),
                VIDEO_CALL_REQUEST_JOINED => tr('VIDEO_CALL_REQUEST_JOINED'),
                VIDEO_CALL_REQUEST_ENDED => tr('VIDEO_CALL_REQUEST_ENDED'),
                ];

    return isset($status_list[$status]) ? $status_list[$status] : tr('VIDEO_CALL_REQUEST_SENT');
}

function get_video_end($video_url) {
    $url = explode('/',$video_url);
    $result = end($url);
    return $result;
}

function getUserTime($time, $timezone = "Asia/Kolkata", $format = "H:i:s") {

    if ($timezone) {

        $new_str = new DateTime($time, new DateTimeZone('UTC') );

        $new_str->setTimeZone(new DateTimeZone( $timezone ));

        return $new_str->format($format);

    }
}
function get_extension_from_path($file) {

    $path_explode = explode('/',$file);

    $file_parameters = explode('.', end($path_explode));

    return end($file_parameters);

}


function getMinutesBetweenTime($startTime, $endTime) {

    $to_time = strtotime($endTime);

    $from_time = strtotime($startTime);

    $diff = abs($to_time - $from_time);

    if ($diff <= 0) {

        $diff = 0;

    } else {

        $diff = round($diff/60);

    }

    return $diff;

}

function formatted_live_payment_text($type = FREE_VIDEO) {

    return $type == FREE_VIDEO ? tr('free_video') : tr('paid_video');
}

function live_video_status($status = VIDEO_STREAMING_ONGOING) {

    return $status == VIDEO_STREAMING_ONGOING ? tr('streaming_live') : tr('streaming_stopped');
}



function common_server_date($date , $timezone = "" , $format = "d M Y h:i A") {

    if($date == "0000-00-00 00:00:00" || $date == "0000-00-00" || !$date) {

        return $date = '';
    }

    if($timezone) {

        $date = convertTimeToUTCzone($date, $timezone, $format);

    }

    return $timezone ? $date : date($format, strtotime($date));

}
// Using php image filters
function generate_blur_file($file) {

    try {

        $extension = get_extension_from_path($file);

        $image = $extension == 'png' ? imagecreatefrompng($file) : imagecreatefromjpeg($file);

        list($width, $height) = getimagesize($file);

        $image = imagescale($image , $width/40, $height/40);
        
        imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);

        // imagefilter($image, IMG_FILTER_SMOOTH,99);

        // imagefilter($image, IMG_FILTER_BRIGHTNESS, 99); 

        // imagefilter($image, IMG_FILTER_PIXELATE, 100, true);

        // imagefilter($image, IMG_FILTER_SCATTER, 100, 150);

        $image = imagescale($image , $width, $height);

        imagejpeg($image, $file);

        imagedestroy($image);

        return true;

    } catch (Exception $e) {

        \Log::info($e->getMessage());

        return true;

    }

}

function height_formatted($height) {

    $height = $height ?? 0;

    return $height.' CM';

}

function weight_formatted($weight) {

    $weight = $weight ?? 0;

    return $weight.' '.tr('pounds');

}


function following_users($user_id){

    $following_user_ids = \App\Models\Follower::where('follower_id',$user_id)->where('status', YES)->pluck('user_id')->toArray() ?? [];

    return $following_user_ids;
}

 function placeholder_path_formate($key) {

    $formate = 'jpg';

    $filepath = 'jpg';

    $filepath = PUBLIC_COMMON_FILE_PATH;

    if ($key == 'profile_placeholder') {
        
        $formate = 'jpeg';
    }

    if ($key == 'profile_placeholder' || $key == 'cover_placeholder') {
        
        $filepath = PUBLIC_HOME;
    }
    
    $status_list = [
                'profile_placeholder' => 'placeholder',
                'cover_placeholder' => 'cover',
                'post_image_placeholder' => 'post-image',
                'video_call_placeholder' => 'video-call',
                'audio_call_placeholder' => 'audio-call',
                'ppv_image_placeholder' => 'ppv-image',
                'ppv_audio_placeholder' => 'ppv-audio',
                'ppv_video_placeholder' => 'ppv-video',
                'post_video_placeholder' => 'post-video',
                'live_streaming_placeholder_img' => 'live-streaming',
                ];

    $data['file_name'] = isset($status_list[$key]) ? $status_list[$key] : tr('placeholder');

    $data['formate'] = $formate;

    $data['filepath'] = '/'.$filepath;

    return $data;   
}

function last_x_months_content_creator_data($months, $user_id) {

    $data = new \stdClass;

    $data->currency = $currency = Setting::get('currency', '$');

    $last_x_days_revenues = [];

    $start  = new \DateTime('-6 month', new \DateTimeZone('UTC'));
    
    $period = new \DatePeriod($start, new \DateInterval('P1M'), $months);
   
    $dates = $last_x_days_revenues = $last_x_days_month = $last_x_days_earning = [];

    $post_ids = \App\Models\Post::where('user_id', $user_id)->pluck('id');

    foreach ($period as $date) {

        $current_month = $date->format('M');

        $formatted_month = $date->format('M Y');

        $last_x_days_data =  new \stdClass;

        $last_x_days_data->month= $current_month;

        $last_x_days_data->formatted_month = $formatted_month;

        $month = $date->format('m');

        $year = $date->format('Y');

        $sum_value = Setting::get('is_only_wallet_payment') ? 'user_token' : 'user_amount';
      
        $last_x_days_subscription_earnings = \App\Models\UserSubscriptionPayment::where('to_user_id',$user_id)
                                            ->whereMonth('paid_date', '=', $month)->whereYear('paid_date', $year)
                                            ->where('status' , PAID)->sum($sum_value);

        $last_x_days_order_earnings =  \App\Models\UserWalletPayment::
                                        where(['user_id' => $user_id, 'usage_type' => USAGE_TYPE_ORDER, 'payment_type' => WALLET_PAYMENT_TYPE_CREDIT, 'status' => PAID])
                                        ->whereMonth('paid_date', '=', $month)->whereYear('paid_date', $year)->where('status' , PAID)
                                        ->sum($sum_value);

        $last_x_days_post_earnings = \App\Models\PostPayment::whereIn('post_id', $post_ids)
                                    ->whereMonth('paid_date', '=', $month)->whereYear('paid_date', $year)->where('status' , PAID)
                                    ->sum($sum_value);

        $last_x_days_user_tips = \App\Models\UserTip::where('to_user_id', $user_id)
                                ->whereMonth('paid_date', '=', $month)->whereYear('paid_date', $year)->where('status',PAID)
                                ->sum($sum_value);

        $last_x_days_video_call = \App\Models\VideoCallPayment::where('model_id', $user_id)
                                ->whereMonth('paid_date', '=', $month)->whereYear('paid_date', $year)->where('status',PAID)
                                ->sum($sum_value);

        $last_x_days_audio_call = \App\Models\AudioCallPayment::where('model_id', $user_id)
                                ->whereMonth('paid_date', '=', $month)->whereYear('paid_date', $year)->where('status',PAID)
                                ->sum($sum_value);

        $last_x_days_chat_asset = \App\Models\ChatAssetPayment::where('from_user_id', $user_id)
                                ->whereMonth('paid_date', '=', $month)->whereYear('paid_date', $year)->where('status',PAID)
                                ->sum($sum_value);

        $last_x_days_live_video = \App\Models\LiveVideoPayment::where(['user_id' => $user_id, 'status' => PAID])
                                ->whereMonth('created_at', '=', $month)->whereYear('created_at', $year)
                                ->sum($sum_value);
        
        $last_x_days_data->subscription_earnings = $last_x_days_subscription_earnings ?: 0.00;

        $last_x_days_data->order_earnings = $last_x_days_order_earnings ?: 0.00;

        $last_x_days_data->post_earnings = $last_x_days_post_earnings ?: 0.00;

        $total_earning = ($last_x_days_subscription_earnings + $last_x_days_order_earnings + $last_x_days_user_tips + $last_x_days_post_earnings + $last_x_days_video_call + $last_x_days_audio_call + $last_x_days_chat_asset + $last_x_days_live_video) ?? 0.00;

        array_push($last_x_days_revenues, $last_x_days_data);

        array_push($last_x_days_month, $formatted_month);

        array_push($last_x_days_earning, $total_earning);

    }
    
    $data->last_x_days_revenues = $last_x_days_revenues;

    $data->last_x_days_month = $last_x_days_month;

    $data->last_x_days_earning = $last_x_days_earning;
    
    return $data;  
}

function stories_status_text($status) {

    return $status == STORIES_APPROVED ? tr('approved') : tr('declined');

}

function get_card_picture($card_type) {

    return asset('/images/cards/'.$card_type.'.png');

}

function last_week_calculations($last_week_payments, $current_week_payments) {

    $data = new \stdClass;

    $data->last_7_days_status = 0;

    $data->amount_type = NO_CHANGE;

    $data->last_week_payments = $last_week_payments;

    $data->current_week_payments = $current_week_payments;

    if($current_week_payments > $last_week_payments) {

        $data->last_7_days_status = round(($current_week_payments - $last_week_payments) / $current_week_payments * 100, 1);

        $data->amount_type = INCREMENT;

    } elseif ($last_week_payments > $current_week_payments) {

        $data->last_7_days_status = round(($last_week_payments - $current_week_payments) / $last_week_payments * 100, 1);

        $data->amount_type = DECREMENT;

    } 

    return $data;
}