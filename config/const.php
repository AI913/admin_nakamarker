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
    'marker_type_register_name'   => '登録マーカー',
    'marker_type_function_name'   => '機能マーカー',
    'marker_type_search_name'     => '検索マーカー',

    // 画像サイズ(一番大きいサイズ ×3, 中ぐらい ×2, 小 ×1)
    'resize_width'      => 320, // リサイズ幅
    'resize_height'     => 240, // リサイズ高さ

    // 画像名
    'no_image'  => 'no_image.png',
    'out_image' => 'out_images.png',

    // コミュニティ申請状況
    'community_history_apply'            => 1,
    'community_history_approval'         => 2,
    'community_history_reject'           => 3,
    'community_history_apply_name'       => '申請中',
    'community_history_approval_name'    => '承認済み',
    'community_history_reject_name'      => '却下',

    // ポイント付与種別
    'point_buy'             => 1,
    'point_gift'            => 2,
    'point_advertise'       => 3,
    'point_admin'           => 4,
    'point_buy_name'        => '購入',
    'point_gift_name'       => 'ギフト',
    'point_advertise_name'  => '広告',
    'point_admin_name'      => '管理者特権',

    // ポイント有料フラグ種別
    'charge_flg_off'            => 1,
    'charge_flg_on'             => 2,
    'charge_flg_default'        => 3,
    'charge_flg_off_name'       => '無料',
    'charge_flg_on_name'        => '有料',
    'charge_flg_default_name'   => 'デフォルト',
    
    // ニュース情報種別
    'official_type'        => 1,
    'community_type'       => 2,
    'official_type_name'   => '公式情報',
    'community_type_name'  => 'コミュニティ情報',

    // プッシュ通知種別
    'push_all'              => 1,
    'push_condition'        => 2,
    'push_all_name'         => '全員',
    'push_condition_name'   => '条件あり',

    //プッシュ通知状況
    'push_before'           => 1,
    'push_now'              => 2,
    'push_after'            => 3,
    'push_error'            => 9,
    'push_before_name'      => '送信前',
    'push_now_name'         => '送信中',
    'push_after_name'       => '送信後',
    'push_error_name'       => '送信エラー',

];
