<?php
use Illuminate\Support\Facades\Route;

Route::get('Admin',  function () {
    return redirect()->route('admin.login');
});

Route::group(['namespace' => 'Auth'], function () {
    # Login Routes
    Route::get('login',     'LoginController@showLoginForm')->name('login');
    Route::post('login',    'LoginController@login');
    Route::get('logout',   'LoginController@logout')->name('logout');
});
 Route::group(['middleware' => 'auth:admin', ''],function(){
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
  
    });