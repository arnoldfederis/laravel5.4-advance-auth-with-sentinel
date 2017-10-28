<?php

Route::group(['namespace' => 'Backend'], function() {

    /* Home Route */
    Route::get('/', 'IndexController@index')->name('admin.index');

});