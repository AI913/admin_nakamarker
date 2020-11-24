<?php

use Illuminate\Http\Request;

/************************************************
 *  アプリ側ルーティング(ログイン)
 ************************************************/
Route::post('/login', 'Api\AuthController@login');
Route::post('/user/create', 'Api\UserController@create');
Route::post('/user/register', 'Api\UserController@register');
Route::post('/password', 'Api\UserController@register');
