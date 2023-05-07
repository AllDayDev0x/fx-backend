<?php

Route::group(['middleware' => 'web'], function() {

    Route::group(['as' => 'support_member.', 'prefix' => 'support'], function(){

        Route::get('clear-cache', function() {

            $exitCode = Artisan::call('config:cache');

            return back();

        })->name('clear-cache');

        Route::get('login', 'Auth\SupportMemberLoginController@showLoginForm')->name('login');

        Route::post('login', 'Auth\SupportMemberLoginController@login')->name('login.post');

        Route::get('logout', 'Auth\SupportMemberLoginController@logout')->name('logout');

        Route::get('profile', 'SupportMemberController@profile')->name('profile');

        Route::post('profile/save', 'SupportMemberController@profile_save')->name('profile.save');

        Route::post('change/password', 'SupportMemberController@change_password')->name('change.password');

        Route::get('/', 'SupportMemberController@dashboard')->name('dashboard');

        Route::get('/password/reset','Auth\SupportMemberForgotPasswordController@showLinkRequestForm')->name('password.request');
        
        Route::post('/password/email','Auth\SupportMemberForgotPasswordController@sendResetLinkEmail')->name('password.email');

        Route::get('/support_tickets/index','SupportMemberController@support_tickets_index')->name('support_tickets.index');

        Route::get('/support_tickets/view','SupportMemberController@support_tickets_view')->name('support_tickets.view');

        Route::get('/support_tickets/chat','SupportMemberController@support_tickets_chat')->name('support_tickets.chat');

    
    });
    
});