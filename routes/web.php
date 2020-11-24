<?php

Route::get('/logout',   'Admin\Auth\LoginController@logout')->name('admin/logout');

/************************************************
 *  管理者画面ルーティング(非ログイン)
 ************************************************/
Route::middleware('guest:admin')->group(function(){
    Route::get('/',   'Admin\Auth\LoginController@showLoginForm')->name('admin/login');
    Route::get('/login',   'Admin\Auth\LoginController@showLoginForm')->name('admin/login');
    Route::post('/login',   'Admin\Auth\LoginController@login')->name('admin/login/submit');
});
/************************************************
 *  管理者画面ルーティング(ログイン)
 ************************************************/
Route::middleware('auth:admin')->group(function () {
    
    /******************** HOME ********************/
    Route::get('/',     'Admin\HomeController@index')->name('admin');
    Route::get('/home', 'Admin\HomeController@index')->name('admin/home');
    Route::get('/test/login',     'Admin\HomeController@testLogin')->name('admin/testLogin');
    Route::get('/test/done',     'Admin\HomeController@testDone')->name('admin/testDone');

    /******************** 各機能一覧データ取得(ajax) ********************/
    Route::get('/ajax/user',              'Admin\UserController@main_list');
    Route::get('/ajax/marker',            'Admin\MarkerController@main_list');
    Route::get('/ajax/community',         'Admin\CommunityController@main_list');
    Route::get('/ajax/news',              'Admin\NewsController@main_list');
    Route::get('/ajax/push',              'Admin\PushHistoryController@main_list');
    Route::get('/ajax/config',            'Admin\ConfigController@main_list');

    // メールアドレス重複チェック
    Route::post('/ajax/is_duplicate_email','Admin\Ajax\AdminAjaxController@isDuplicateEmail');

    /******************** ユーザ管理(users) ********************/
    Route::get('/user',             'Admin\UserController@index')->name('admin/user');
    Route::get('/user/create',      'Admin\UserController@create')->name('admin/user/create');
    Route::get('/user/edit/{id}',   'Admin\UserController@edit')->name('admin/user/edit');
    Route::get('/user/detail/{id}', 'Admin\UserController@detail')->name('admin/user/detail');
    Route::post('/user/save',       'Admin\UserController@save')->name('admin/user/save');
    Route::post('/user/remove',     'Admin\UserController@remove')->name('admin/user/remove');

    /******************** ユーザロケーション管理(user_locations & ajax) ********************/
    // ユーザの登録場所管理(user_locations)
    Route::get('/ajax/user/detail/{id}/location',  'Admin\UserController@user_locations')->name('admin/user/detail/location');
    // ユーザの登録場所情報を詳細に取得(user_locations)
    Route::get('/ajax/user/detail/{id}/location/{location_id}',  'Admin\UserController@user_locations_detail')->name('admin/user/detail/location/detail');
    // ユーザの登録場所イメージ更新(user_locations)
    Route::post('/ajax/user/detail/{id}/location/{location_id}/image',  'Admin\UserController@user_locationImage_delete')->name('admin/user/details/location/image');
    // ユーザの登録場所削除処理(user_locations)
    Route::post('/user/detail/{id}/location/remove',     'Admin\UserController@removeLocation')->name('admin/user/details/location/remove');
    
    /******************** ユーザその他(ajax) ********************/
    // ユーザの所有マーカー一覧(user_markers)
    Route::get('/ajax/user/detail/{id}/marker',     'Admin\UserController@user_markers')->name('admin/user/detail/marker');
    // 特定ユーザのポイント履歴管理(user_points_histories)
    Route::get('/ajax/user/detail/{id}/point', 'Admin\UserController@point_histories')->name('admin/user/detail/point');
    // 特定ユーザのポイント更新(user_points_histories)
    Route::post('/ajax/user/detail/{id}/point/update', 'Admin\UserController@updatePoints')->name('admin/user/point/update');
    // ユーザの参加コミュニティ管理(community_histories)
    Route::get('/ajax/user/detail/{id}/community',  'Admin\UserController@user_communities')->name('admin/user/detail/community');
    // ユーザの所有マーカー削除(user_markers)
    Route::post('/user/detail/{id}/marker/remove',  'Admin\UserController@removeMarker')->name('admin/user/detail/marker/remove');
    // 特定ユーザのポイント削除(user_points_histories)
    Route::post('/user/detail/{id}/point/remove',  'Admin\UserController@removePoint')->name('admin/user/detail/point/remove');


    // 特定ユーザのポイント消費(削除予定)
    Route::post('/ajax/user/detail/{id}/point/pay', 'Admin\UserController@pay_points')->name('admin/user/point/pay');

    
    /******************** マーカー管理(markers) ********************/
    Route::get('/marker',             'Admin\MarkerController@index')->name('admin/marker');
    Route::get('/marker/create',      'Admin\MarkerController@create')->name('admin/marker/create');
    Route::get('/marker/edit/{id}',   'Admin\MarkerController@edit')->name('admin/marker/edit');
    Route::get('/marker/detail/{id}', 'Admin\MarkerController@detail')->name('admin/marker/detail');
    Route::post('/marker/save',       'Admin\MarkerController@save')->name('admin/marker/save');
    Route::post('/marker/remove',     'Admin\MarkerController@remove')->name('admin/marker/remove');

    /******************** マーカーその他(ajax) ********************/
    // マーカーの所有ユーザ一覧(user_markers)
    Route::get('/ajax/marker/detail/{id}/user',     'Admin\MarkerController@marker_users')->name('admin/marker/detail/user');
    
    /******************** コミュニティ管理(communities) ********************/
    Route::get('/community',             'Admin\CommunityController@index')->name('admin/community');
    Route::get('/community/create',      'Admin\CommunityController@create')->name('admin/community/create');
    Route::get('/community/edit/{id}',   'Admin\CommunityController@edit')->name('admin/community/edit');
    Route::get('/community/detail/{id}', 'Admin\CommunityController@detail')->name('admin/community/detail');
    Route::post('/community/save',       'Admin\CommunityController@save')->name('admin/community/save');
    Route::post('/community/remove',     'Admin\CommunityController@remove')->name('admin/community/remove');

    // コミュニティの所属ユーザ管理(community_histories)
    Route::get('/ajax/community/detail/{id}/user',  'Admin\CommunityController@community_users')->name('admin/community/detail/user');
    // コミュニティの所属ユーザ管理(community_locations)
    Route::get('/ajax/community/detail/{id}/user/{user_id}',  'Admin\CommunityController@community_users_detail')->name('admin/community/detail/user/detail');
    // コミュニティの登録場所管理(community_locations)
    Route::get('/ajax/community/detail/{id}/location',  'Admin\CommunityController@community_locations')->name('admin/community/detail/location');

    /******************** コミュニティロケーション管理(community_locations) ********************/
    Route::get('/community/detail/{id}/location',                         'Admin\CommunityLocationController@index')->name('admin/community/detail/location/index');
    Route::get('/community/detail/{id}/location/create',                  'Admin\CommunityLocationController@create')->name('admin/community/detail/location/create');
    Route::get('/community/detail/{id}/location/edit/{location_id}',      'Admin\CommunityLocationController@edit')->name('admin/community/detail/location/edit');
    Route::post('/community/detail/{id}/location/save',                   'Admin\CommunityLocationController@save')->name('admin/community/detail/location/save');
    Route::post('/community/detail/{id}/location/remove',                 'Admin\CommunityLocationController@remove')->name('admin/community/detail/location/remove');

    // 備考データの取得(community_locations)
    Route::get('/ajax/community/detail/{id}/location/detail/{location_id}',    'Admin\CommunityLocationController@getMemo')->name('admin/community/detail/location/detail');

    /******************** コミュニティ履歴管理(community_histories) ********************/
    // コミュニティ詳細管理(community_histories)
    Route::get('/ajax/community/history/{community_history_id}',  'Admin\CommunityHistoryController@detail')->name('admin/community/history/detail/');
    // コミュニティの申請状況更新(community_histories)
    Route::post('/ajax/community/history/update',       'Admin\CommunityHistoryController@updateStatus')->name('admin/community/history/update');
    // コミュニティの申請状況削除(community_histories)
    Route::post('/community/history/remove',     'Admin\CommunityHistoryController@remove')->name('admin/community/history/remove');

    /******************** お知らせ管理(news) ********************/
    Route::get('/news',             'Admin\NewsController@index')->name('admin/news');
    Route::get('/news/create',      'Admin\NewsController@create')->name('admin/news/create');
    Route::get('/news/edit/{id}',   'Admin\NewsController@edit')->name('admin/news/edit');
    Route::get('/news/detail/{id}', 'Admin\NewsController@detail')->name('admin/news/detail');
    Route::post('/news/save',       'Admin\NewsController@save')->name('admin/news/save');
    Route::post('/news/remove',     'Admin\NewsController@remove')->name('admin/news/remove');
    
    /******************** 通知履歴管理(push_histories) ********************/
    Route::get('/push',             'Admin\PushHistoryController@index')->name('admin/push');
    Route::get('/push/create',      'Admin\PushHistoryController@create')->name('admin/push/create');
    Route::get('/push/edit/{id}',   'Admin\PushHistoryController@edit')->name('admin/push/edit');
    Route::get('/push/detail/{id}', 'Admin\PushHistoryController@detail')->name('admin/push/detail');
    Route::post('/push/save',       'Admin\PushHistoryController@save')->name('admin/push/save');
    Route::post('/push/remove',     'Admin\PushHistoryController@remove')->name('admin/push/remove');

    /******************** 共通設定管理(configs) ********************/
    Route::get('/config',             'Admin\ConfigController@index')->name('admin/config');
    Route::get('/config/create',      'Admin\ConfigController@create')->name('admin/config/create');
    Route::get('/config/edit/{id}',   'Admin\ConfigController@edit')->name('admin/config/edit');
    Route::post('/config/save',       'Admin\ConfigController@save')->name('admin/config/save');
    Route::post('/config/remove',     'Admin\ConfigController@remove')->name('admin/config/remove');
});

    
