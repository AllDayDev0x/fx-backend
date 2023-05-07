<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index');

Route::get('/home', 'AdminController@index')->name('home');

Route::get('settings_generate_json', 'ApplicationController@settings_generate_json')->name('settings_generate_json');

Route::get('free_subscriptions_clear', 'UpgradeController@free_subscriptions_clear')->name('free_subscriptions_clear');

Route::group(['middleware' => 'web'], function() {

	Route::any('payment-success' , 'Api\CCBillPaymentController@payment_success');

	Route::any('tips_payment_success' , 'Api\CCBillPaymentController@tips_payment_success')->name('tips_payment_success');

	Route::any('posts_payment_success' , 'Api\CCBillPaymentController@posts_payment_success')->name('posts_payment_success');

	Route::any('subscription_payment_success' , 'Api\CCBillPaymentController@subscription_payment_success')->name('subscription_payment_success');

	Route::any('payment-failure' , 'Api\CCBillPaymentController@tips_payment_failure');

	Route::any('coinpayment-success' , 'Api\CoinPaymentController@payment_success')->name('coinpayment-success');

	Route::any('coinpayment-failure' , 'Api\CoinPaymentController@payment_failure')->name('coinpayment-failure');

	Route::any('tips_coinpayment_success' , 'Api\CoinPaymentController@tips_coinpayment_success')->name('tips_coinpayment_success');

	Route::any('posts_coinpayment_success' , 'Api\CoinPaymentController@posts_coinpayment_success')->name('posts_coinpayment_success');

	Route::any('subscription_coinpayment_success' , 'Api\CoinPaymentController@subscription_coinpayment_success')->name('subscription_coinpayment_success');

	Route::any('live_video_coinpayment_success' , 'Api\CoinPaymentController@live_video_coinpayment_success')->name('live_video_coinpayment_success');

});
