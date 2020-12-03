<?php

use Illuminate\Http\Request;

/************************************************
 *  アプリ側ルーティング(非ログイン)
 ************************************************/
Route::post('/user/create', 'Api\UserController@create');
Route::post('/login', 'Api\UserController@login');

/************************************************
 *  アプリ側ルーティング(ログイン)
 ************************************************/
Route::middleware('app.auth')->group(function(){

    /******************** ユーザ管理(users) ********************/
    Route::post('/user',                    'Api\UserController@info');
    Route::post('/user/update',             'Api\UserController@update');
    Route::post('/user/register',           'Api\UserController@register');
    Route::post('/password',                'Api\UserController@password');
    /******************** ユーザポイント管理(user_points_histories) ********************/
    Route::post('/user/point',              'Api\UserController@pointInfo');
    Route::post('/user/point/update',       'Api\UserController@pointUpdate');
    /******************** ユーザロケーション管理(user_locations) ********************/
    Route::post('/user/location',           'Api\UserController@locationInfo');
    Route::post('/user/location/register',  'Api\UserController@locationRegister');
    Route::post('/user/location/remove',    'Api\UserController@locationRemove');

    /******************** 共通設定管理(configs) ********************/
    Route::post('/config',                  'Api\ConfigController@index');
});
