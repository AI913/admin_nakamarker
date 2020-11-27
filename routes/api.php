<?php

use Illuminate\Http\Request;

/************************************************
 *  アプリ側ルーティング(ログイン)
 ************************************************/
Route::post('/user/create', 'Api\UserController@create');
Route::post('/login', 'Api\UserController@login');

Route::middleware('app.auth')->group(function(){
    Route::post('/user', 'Api\UserController@info');
    Route::post('/user/point', 'Api\UserController@pointInfo');
    Route::post('/user/update', 'Api\UserController@update');
    Route::post('/user/register', 'Api\UserController@register');
    Route::post('/password', 'Api\UserController@password');
    Route::post('/config', 'Api\ConfigController@index');
});
