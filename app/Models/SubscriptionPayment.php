<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $appends = ['subscription_amount_formatted', 'amount_formatted','plan_formatted'];

    public function getSubscriptionAmountFormattedAttribute() {

        return formatted_amount($this->subscription_amount ?? 0.00);
    }

    public function getAmountFormattedAttribute() {

        return formatted_amount($this->amount  ?? 0.00);
    }

    public function getPlanFormattedAttribute() {

        return formatted_plan($this->plan,$this->plan_type);
    }

    public function user() {
    	return $this->belongsTo('App\Models\User','user_id');
    }

    public function subscription() {
    	return $this->belongsTo('App\Models\Subscription', 'subscription_id');
    }

    /**
     * Scope a query to basic subscription details
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBaseResponse($query) {

        $currency = \Setting::get('currency' , '$');

    	return $query->leftJoin('subscriptions', 'subscriptions.id', '=', 'subscription_id')
            ->select(
	            'subscription_payments.id as user_subscription_id',
	            'subscriptions.title',
	            'subscriptions.description',
	            'subscriptions.is_popular',
	            'subscriptions.plan',
	            'subscription_payments.*',
	            \DB::raw("'$' as currency")
            );
    }
    

     /**
     * Scope a query to basic user subscription details
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeCommonResponse($query) {

        return $query->join('users','users.id','=','subscription_payments.user_id')
               ->join('subscriptions','subscriptions.id','=','subscription_payments.subscription_id')
               ->select(
                   'subscription_payments.*',
                   'subscriptions.title as subscription_name',
                   'users.name as user_name',
                   'users.mobile',
                   'users.email'
                );
    }
}
