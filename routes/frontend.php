<?php

Route::group(['namespace' => 'Frontend'], function() {

    Route::get('@{username}', 'Users\ProfileController@show')->name('user.profile');
    /* Home Route */
    Route::get('/', 'IndexController@index');

});