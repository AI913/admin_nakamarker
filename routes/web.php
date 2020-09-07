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

    /******************** 各機能一覧データ取得 ********************/
    Route::get('/ajax/user',              'Admin\UserController@main_list');
    Route::get('/ajax/marker',            'Admin\MarkerController@main_list');
    Route::get('/ajax/community',         'Admin\CommunityController@main_list');
    Route::get('/ajax/user-location',     'Admin\UserLocationController@main_list');
    Route::get('/ajax/community-location',     'Admin\CommunityLocationController@main_list');
    Route::get('/ajax/user-points-history',     'Admin\UserPointsHistoryController@main_list');
    Route::get('/ajax/community-history',     'Admin\CommunityHistoryController@main_list');
    Route::get('/ajax/news',     'Admin\NewsController@main_list');

    // メールアドレス重複チェック
    Route::post('/ajax/is_duplicate_email','Admin\Ajax\AdminAjaxController@isDuplicateEmail');

    /******************** ユーザ管理(users) ********************/
    Route::get('/user',             'Admin\UserController@index')->name('admin/user');
    Route::get('/user/create',      'Admin\UserController@create')->name('admin/user/create');
    Route::get('/user/edit/{id}',   'Admin\UserController@edit')->name('admin/user/edit');
    Route::get('/user/detail/{id}', 'Admin\UserController@detail')->name('admin/user/detail');
    Route::post('/user/save',       'Admin\UserController@save')->name('admin/user/save');
    Route::post('/user/remove',     'Admin\UserController@remove')->name('admin/user/remove');

    // ユーザの登録場所管理(user_locations)
    Route::get('/ajax/user/detail/{id}/user_locations',  'Admin\UserController@user_locations')->name('admin/user/detail/user_locations');
    // ユーザの登録場所情報を詳細に取得(user_locations)
    Route::get('/ajax/user/detail/{id}/user_locations/{location_id}',  'Admin\UserController@user_locations_detail')->name('admin/user/detail/user_locations_detail');
    // ユーザの登録場所イメージ更新(user_locations)
    Route::post('/ajax/user-location/location_images',  'Admin\UserController@user_locationImage_delete')->name('admin/user-location/location_images');
    // 特定ユーザのポイント履歴管理(user_points_histories)
    Route::get('/user-points-history/detail/{id}/point_histories', 'Admin\UserController@point_histories')->name('admin/user-points-history/detail/point_histories');
    // 特定ユーザのポイント更新(user_points_histories)
    Route::post('/ajax/user-points-history/update_points', 'Admin\UserController@updatePoints')->name('admin/user-points-history/update_points');
    // ユーザの参加コミュニティ管理(community_histories)
    Route::get('/ajax/user/detail/{id}/user_communities',  'Admin\UserController@user_communities')->name('admin/user/detail/user_communities');
    
    /******************** マーカー管理(markers) ********************/
    Route::get('/marker',             'Admin\MarkerController@index')->name('admin/marker');
    Route::get('/merker/create',      'Admin\MarkerController@create')->name('admin/marker/create');
    Route::get('/marker/edit/{id}',   'Admin\MarkerController@edit')->name('admin/marker/edit');
    Route::get('/marker/detail/{id}', 'Admin\MarkerController@detail')->name('admin/marker/detail');
    Route::post('/marker/save',       'Admin\MarkerController@save')->name('admin/marker/save');
    Route::post('/marker/remove',     'Admin\MarkerController@remove')->name('admin/marker/remove');

    // マーカーの所有ユーザ一覧(user_markers)
    Route::get('/ajax/marker/detail/{id}/marker_users',     'Admin\MarkerController@marker_users')->name('admin/marker/marker_users');
    
    /******************** コミュニティ管理(communities) ********************/
    Route::get('/community',             'Admin\CommunityController@index')->name('admin/community');
    Route::get('/community/create',      'Admin\CommunityController@create')->name('admin/community/create');
    Route::get('/community/edit/{id}',   'Admin\CommunityController@edit')->name('admin/community/edit');
    Route::get('/community/detail/{id}', 'Admin\CommunityController@detail')->name('admin/community/detail');
    Route::post('/community/save',       'Admin\CommunityController@save')->name('admin/community/save');
    Route::post('/community/remove',     'Admin\CommunityController@remove')->name('admin/community/remove');

    // コミュニティの所属ユーザ管理(community_histories)
    Route::get('/ajax/community/detail/{id}/community_users',  'Admin\CommunityController@community_users')->name('admin/community/detail/community_users');
    // コミュニティの所属ユーザ管理(community_locations)
    Route::get('/ajax/community/detail/{id}/community_users/{user_id}',  'Admin\CommunityController@community_users_detail')->name('admin/community/detail/community_users_detail');
    // コミュニティの登録場所管理(community_locations)
    Route::get('/ajax/community/detail/{id}/community_locations',  'Admin\CommunityController@community_locations')->name('admin/community/detail/community_locations');
    // コミュニティの登録場所管理(community_locations)
    Route::get('/ajax/community/detail/{id}/community_locations/{location_id}',  'Admin\CommunityController@community_locations_detail')->name('admin/community/detail/community_locations_detail');

    /******************** ユーザロケーション管理(user_locations) ********************/
    Route::get('/user-location',             'Admin\UserLocationController@index')->name('admin/user-location');
    Route::get('/user-location/edit/{id}',   'Admin\UserLocationController@edit')->name('admin/user-location/edit');
    Route::get('/user-location/detail/{id}', 'Admin\UserLocationController@detail')->name('admin/user-location/detail');
    Route::post('/user-location/save',       'Admin\UserLocationController@save')->name('admin/user-location/save');
    Route::post('/user-location/remove',     'Admin\UserLocationController@remove')->name('admin/user-location/remove');

    /******************** コミュニティロケーション管理(community_locations) ********************/
    Route::get('/community-location',             'Admin\CommunityLocationController@index')->name('admin/community-location');
    Route::get('/community-location/create',      'Admin\CommunityLocationController@create')->name('admin/community-location/create');
    Route::get('/community-location/edit/{id}',   'Admin\CommunityLocationController@edit')->name('admin/community-location/edit');
    Route::get('/community-location/detail/{id}', 'Admin\CommunityLocationController@detail')->name('admin/community-location/detail');
    Route::post('/community-location/save',       'Admin\CommunityLocationController@save')->name('admin/community-location/save');
    Route::post('/community-location/remove',     'Admin\CommunityLocationController@remove')->name('admin/community-location/remove');

    /******************** ポイント履歴管理(user_points_histories) ********************/
    Route::get('/user-points-history',             'Admin\UserPointsHistoryController@index')->name('admin/user-points-history');
    // Route::get('/user-points-history/create',      'Admin\UserPointsHistoryController@create')->name('admin/user-points-history/create');
    Route::get('/user-points-history/edit/{id}',   'Admin\UserPointsHistoryController@edit')->name('admin/user-points-history/edit');
    Route::get('/user-points-history/detail/{id}', 'Admin\UserPointsHistoryController@detail')->name('admin/user-points-history/detail');
    Route::post('/user-points-history/save',       'Admin\UserPointsHistoryController@save')->name('admin/user-points-history/save');
    Route::post('/user-points-history/remove',     'Admin\UserPointsHistoryController@remove')->name('admin/user-points-history/remove');
    // 特定ユーザのポイント履歴管理
    // Route::get('/user-points-history/detail/{user_id}/point_histories', 'Admin\UserPointsHistoryController@point_histories')->name('admin/user-points-history/detail/point_histories');
    // // 特定ユーザのポイント更新
    Route::post('/ajax/user-points-history/update_points', 'Admin\UserPointsHistoryController@updatePoints')->name('admin/user-points-history/update_points');

    /******************** コミュニティ履歴管理(community_histories) ********************/
    Route::get('/community-history',             'Admin\CommunityHistoryController@index')->name('admin/community-history');
    // Route::get('/community-history/create',      'Admin\CommunityHistoryController@create')->name('admin/community-history/create');
    Route::get('/community-history/edit/{id}',   'Admin\CommunityHistoryController@edit')->name('admin/community-history/edit');
    Route::get('/community-history/detail/{id}', 'Admin\CommunityHistoryController@detail')->name('admin/community-history/detail');
    Route::post('/community-history/save',       'Admin\CommunityHistoryController@save')->name('admin/community-history/save');
    Route::post('/community-history/remove',     'Admin\CommunityHistoryController@remove')->name('admin/community-history/remove');
    // コミュニティの申請状況管理(community)
    Route::get('/ajax/community-history/detail/{id}/entry_histories',  'Admin\CommunityHistoryController@entry_histories')->name('admin/community-history/detail/entry_histories');
    // コミュニティの申請状況更新(community)
    Route::post('/ajax/community-history/update_status',       'Admin\CommunityHistoryController@updateStatus')->name('admin/community-history/update_status');

    /******************** お知らせ管理(news) ********************/
    Route::get('/news',             'Admin\NewsController@index')->name('admin/news');
    Route::get('/news/create',      'Admin\NewsController@create')->name('admin/news/create');
    Route::get('/news/edit/{id}',   'Admin\NewsController@edit')->name('admin/news/edit');
    Route::get('/news/detail/{id}', 'Admin\NewsController@detail')->name('admin/news/detail');
    Route::post('/news/save',       'Admin\NewsController@save')->name('admin/news/save');
    Route::post('/news/remove',     'Admin\NewsController@remove')->name('admin/news/remove');
    
    /******************** 通知履歴管理(push_histories) ********************/
    Route::get('/push',             'Admin\PushController@index')->name('admin/push');
    Route::get('/push/create',      'Admin\PushController@create')->name('admin/push/create');
    Route::get('/push/edit/{id}',   'Admin\PushController@edit')->name('admin/push/edit');
    Route::get('/push/detail/{id}', 'Admin\PushController@detail')->name('admin/push/detail');
    Route::post('/push/save',       'Admin\PushController@save')->name('admin/push/save');
    Route::post('/push/remove',     'Admin\PushController@remove')->name('admin/push/remove');

    /******************** 共通設定管理(configs) ********************/
    Route::get('/config',             'Admin\ConfigController@index')->name('admin/config');
    Route::get('/config/create',      'Admin\ConfigController@create')->name('admin/config/create');
    Route::get('/config/edit/{id}',   'Admin\ConfigController@edit')->name('admin/config/edit');
    Route::post('/config/save',       'Admin\ConfigController@save')->name('admin/config/save');
    Route::post('/config/remove',     'Admin\ConfigController@remove')->name('admin/config/remove');
});

    
