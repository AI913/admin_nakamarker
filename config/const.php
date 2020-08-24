<?php
return [
    // アプリケーションバージョン番号　
    'app_version' => "1.0.17" ,
    /*-------------------------------------その他-----------------------------------*/

    /* 都道府県 */
    'pref' => ["北海道","青森県","岩手県","秋田県","宮城県","山形県","福島県","新潟県",
            "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県","長野県","山梨県",
            "静岡県","岐阜県","愛知県","富山県","石川県","福井県","滋賀県","三重県","京都府",
            "奈良県","和歌山県","大阪府","兵庫県","岡山県","鳥取県","島根県","広島県","山口県",
            "香川県","愛媛県","徳島県","高知県","福岡県","佐賀県","大分県","長崎県","熊本県",
            "宮崎県","鹿児島県","沖縄県","海外"
    ],

    'csv_register_mode'         => 2,

    // 公開フラグ
    'private'       => 0,         // 非公開
    'open'          => 1,         // 公開
    'private_name'  => '非公開',  // 非公開
    'open_name'     => '公開',    // 公開

    // ユーザステータス
    'user_app_member'            => 1,                // (一般)会員ユーザー
    'user_app_unsubscribe'       => 2,                // (一般)退会済み
    'user_admin_system'          => 3,                // (管理者)運営管理者
    'user_app_account_stop'      => 4,                // (一般)アカウント停止
    'user_app_member_name'       => '会員',           // (一般)会員ユーザー
    'user_app_unsubscribe_name'  => '退会済み',       // (一般)退会済み
    'user_admin_system_name'     => '運営管理者',     // (管理者)運営管理者
    'user_app_account_stop_name' => 'アカウント停止',  // (一般)アカウント停止

    'point_type_give'  => 1,   // ポイント種別(加算))
    'point_type_pay'   => 2,   // ポイント種別(減少)

    // マーカー種別
    'marker_type_register'        => 1,
    'marker_type_function'        => 2,
    'marker_type_search'          => 3,
    'marker_type_community'       => 4,
    'marker_type_register_name'   => '登録マーカー',
    'marker_type_function_name'   => '機能マーカー',
    'marker_type_search_name'     => '検索マーカー',
    'marker_type_community_name'  => 'コミュニティマーカー',

    // 画像サイズ(一番大きいサイズ ×3, 中ぐらい ×2, 小 ×1)
    'resize_width'      => 320, // リサイズ幅
    'resize_height'     => 240, // リサイズ高さ

    // 画像名
    'no_image'  => 'no_image.png',
    'out_image' => 'out_images.png',

    'default_point_name' => '通常コイン',

    'information_type_app'      => 1,   // アプリ配信用
    'information_type_reserve'  => 2,   // 予約配信用

    'location_type_login'       => 1,   // ロケーション種別：ログイン
    'location_type_store'       => 2,   // ロケーション種別：店舗
    'location_type_geofence'    => 3,   // ロケーション種別：ジオフェンス
    'location_type_change'      => 4,   // ロケーション種別：ポイント交換
    'location_type_birthday'    => 5,   // ロケーション種別：誕生日ボーナス

    'location_type_system'      => 99,  // ロケーション種別：システム付与

    'service_type_delivery'     => 9,   // サービス券配信条件種別：即時配信

    'default_radius'            => 10,  // 半径未入力時のデフォルト半径

    'csv_chunk_size'            => 5000 // csvダウンロード時のchunk用定数

];
