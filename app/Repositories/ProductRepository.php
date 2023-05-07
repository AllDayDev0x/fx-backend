<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\Models\UserProductPicture, App\Models\DeliveryAddress, App\Models\OrderProduct, App\Models\OrderPayment, App\Models\UserProduct;

use App\Models\Cart;

class ProductRepository {

    /**
     * @method orders_list_response()
     *
     * @uses Format the product response
     *
     * @created Subham
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function orders_list_response($orders, $request) {
        
        $orders = $orders->map(function ($order, $key) use ($request) {

                        $order->delivery_address = DeliveryAddress::where('id', $order->delivery_address_id)->first();

                        $order_product = OrderProduct::where('order_id', $order->id)->with('userProductDetails');

                        $order->order_product = $order_product->get();

                        $user_product_ids = $order_product->pluck('user_product_id');

                        $order_payment = OrderPayment::where('order_id', $order->id)->first();

                        $order_payment->paid_date_formatted = common_date($order_payment->paid_date,'','d M Y');

                        $order->order_payment = $order_payment;

                        $order->product_details = UserProduct::whereIn('id', $user_product_ids)->get();

                        return $order;
                    });


        return $orders;

    }

    /**
     * @method order_view_single_response()
     *
     * @uses Format the post response
     *
     * @created Subham
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function order_view_single_response($order, $request) {

        $order->delivery_address = DeliveryAddress::where('id', $order->delivery_address_id)->first();

        $base_query = OrderProduct::where('order_id', $order->id)->with('userProductDetails');

        $order->order_product = $base_query->get();

        $user_product_id = $base_query->pluck('user_product_id');

        $order->product_details = UserProduct::whereIn('id',$user_product_id)->get(); 
        
        $order->order_payment = OrderPayment::where('order_id', $order->id)->first();

        $order->publish_time_formatted = common_date($order->created_at, $request->timezone, 'M d');

        return $order;
    
    }

    /**
     * @method user_products_list_response()
     *
     * @uses Format the product response
     *
     * @created Subham
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_products_list_response($user_products, $request) {
        
        $user_products = $user_products->map(function ($user_product, $key) use ($request) {

                        $user_product->userProductFiles = UserProductPicture::where('user_product_id', $user_product->id)->get();

                        $user_product->publish_time_formatted = common_date($user_product->created_at, $request->timezone, 'M d');

                        $cart = Cart::where('user_product_id', $user_product->id)->where('user_id', $request->id)->first();

                        $user_product->add_to_cart = $cart ? NO : YES ;

                        return $user_product;
                    });


        return $user_products;

    }

    /**
     * @method user_product_single_response()
     *
     * @uses Format the post response
     *
     * @created Subham
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_product_single_response($user_product, $request) {

        $user_product->userProductFiles = UserProductPicture::where('user_product_id', $user_product->id)->get();

        $user_product->publish_time_formatted = common_date($user_product->created_at, $request->timezone, 'M d');

        $cart = Cart::where('user_product_id', $user_product->id)->where('user_id', $request->id)->first();

        $user_product->add_to_cart = $cart ? NO : YES ;

        return $user_product;
    
    }

	/**
     *
     * @method user_product_pictures_save()
     *
     * @uses To Upoad Product Pictures
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param 
     *
     * @return
     */
    public static function user_product_pictures_save($files, $user_product_id) {

        $allowedfileExtension=['jpeg','jpg','png'];

        // Single file upload

        if(!is_array($files)) {
            
            $file = $files;

            $user_product_pictures = new \App\Models\UserProductPicture;

            $user_product_pictures->user_product_id = $user_product_id;

            $user_product_pictures->picture = Helper::storage_upload_file($file, COMMON_FILE_PATH);

            $user_product_pictures->save();

            return true;
       
        }

        // Multiple files upload
        foreach($files as $file) {

            $filename = $file->getClientOriginalName();

            $extension = $file->getClientOriginalExtension();

            $check_picture = in_array($extension, $allowedfileExtension);
            
            if($check_picture) {

                $user_product_pictures = new \App\Models\UserProductPicture;

	            $user_product_pictures->user_product_id = $user_product_id;

	            $user_product_pictures->picture = Helper::storage_upload_file($file, COMMON_FILE_PATH);

	            $user_product_pictures->save();

           }
        
        }

        return true;
    
    }

    /**
     * @method user_products_orders_list_response()
     *
     * @uses Format the product response
     *
     * @created Arun
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_products_orders_list_response($orders, $request) {
        
        $orders = $orders->map(function ($order, $key) use ($request) {

                        $order->delivery_address = DeliveryAddress::where('id', $order->delivery_address_id)->first();

                        $order->order_product = OrderProduct::where('order_id', $order->id)
                                            ->where('user_product_id', $request->user_product_id)
                                            ->with('userProductDetails')
                                            ->first();

                        $order->order_payment = OrderPayment::where('order_id', $order->id)->first();

                        return $order;
                    });


        return $orders;

    }
}