<?php

Route::group(['namespace' => 'Auth'], function() {

    /* Authentication Routes */
    Route::get('login', 'AuthController@login')->name('auth.login');
    Route::post('login', 'AuthController@auth')->name('auth.auth');
    Route::post('logout', 'AuthController@logout')->name('auth.logout');

    /* Registration Routes */
    Route::get('register', 'AuthController@register')->name('auth.register');
    Route::post('register', 'AuthController@store')->name('auth.store');
    Route::get('activate/{email}/{code}', 'AuthController@activate')->name('auth.activate');

    /* Password Reset Routes */
    Route::get('password/forgot', 'AuthController@forgot')->name('auth.forgot');
    Route::post('password/forgot', 'AuthController@request')->name('auth.request');
    Route::get('password/reset/{email?}/{code?}', 'AuthController@reset')->name('auth.reset');
    Route::post('password/reset/{email?}/{code?}', 'AuthController@change')->name('auth.change');

});