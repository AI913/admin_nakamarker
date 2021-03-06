<?php

use Illuminate\Http\Request;

/************************************************
 *  アプリ側ルーティング(非ログイン)
 ************************************************/
Route::post('/user/create',         'Api\UserController@create');
Route::post('/login',               'Api\AuthController@login');
Route::post('/config',              'Api\ConfigController@index');
Route::post('/config/maintenance',  'Api\ConfigController@maintenance');

/************************************************
 *  アプリ側ルーティング(ログイン)
 ************************************************/
Route::middleware('app.auth')->group(function(){

    /******************** 認証管理 ********************/
    Route::post('/password',        'Api\AuthController@password');
    Route::post('/phone_register',  'Api\AuthController@phoneRegister');
    Route::post('/unsubscribe',     'Api\AuthController@unsubscribe');
    /******************** ユーザ管理(users) ********************/
    Route::post('/user',                            'Api\UserController@info');
    Route::post('/user/update',                     'Api\UserController@update');
    /******************** ユーザポイント管理(user_points_histories) ********************/
    Route::post('/user/point',                      'Api\UserController@pointInfo');
    Route::post('/user/point/update',               'Api\UserController@pointUpdate');
    /******************** ユーザロケーション管理(user_locations) ********************/
    Route::post('/user/location',                   'Api\UserController@locationInfo');
    Route::post('/user/location/register',          'Api\UserController@locationRegister');
    Route::post('/user/location/remove',            'Api\UserController@locationRemove');
    Route::post('/user/location/from_marker',       'Api\UserController@getUserLocationFromMarkerId');
    /******************** ユーザマーカー管理(user_markers) ********************/
    Route::post('/user/marker',                     'Api\UserController@getUserMarker');
    Route::post('/user/marker/update',              'Api\UserController@markerUpdate');
    /******************** ユーザコミュニティ管理(community_histories) ********************/
    Route::post('/user/community_history',              'Api\UserController@getUserJoinedCommunity');
    Route::post('/user/community_history/update',       'Api\UserController@communityUpdate');
    Route::post('/user/community_history/unsubscribe',  'Api\UserController@communityUnsubscribe');

    /******************** マーカー管理(markers) ********************/
    Route::post('/marker',                          'Api\MarkerController@getAllMarker');

    /******************** コミュニティ管理(communities) ********************/
    Route::post('/community',                               'Api\CommunityController@getAllCommunity');
    Route::post('/community/register',                      'Api\CommunityController@register');
    Route::post('/community/update',                        'Api\CommunityController@update');
    Route::post('/community/remove',                        'Api\CommunityController@remove');
    /******************** コミュニティマーカー管理(community_markers) ********************/
    Route::post('/community/marker',                        'Api\CommunityController@getCommunityMarkerList');
    Route::post('/community/marker/register',               'Api\CommunityController@markerRegister');
    Route::post('/community/marker/update',                 'Api\CommunityController@markerUpdate');
    Route::post('/community/marker/remove',                 'Api\CommunityController@markerRemove');
    /******************** コミュニティのユーザ管理(community_histories) ********************/
    Route::post('/community/community_history',             'Api\CommunityController@getUserListOfJoinRequest');
    Route::post('/community/community_history/update',      'Api\CommunityController@userUpdate');
    /******************** コミュニティロケーション管理(community_locations) ********************/
    Route::post('/community/location',                      'Api\CommunityController@locationInfo');
    Route::post('/community/location/register',             'Api\CommunityController@locationRegister');
    Route::post('/community/location/remove',               'Api\CommunityController@locationRemove');
    Route::post('/community/location/news',                 'Api\CommunityController@locationNews');

    /******************** ニュース管理(news) ********************/
    Route::post('/news',                                    'Api\NewsController@index');
});
