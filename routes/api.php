<?php

use Illuminate\Http\Request;

/************************************************
 *  アプリ側ルーティング(ログイン)
 ************************************************/
Route::post('/user/create', 'Api\UserController@create');
Route::post('/login', 'Api\UserController@login');

Route::middleware('app.auth')->group(function(){
    Route::post('/user/register', 'Api\UserController@register');
    Route::post('/password', 'Api\UserController@password');
});
