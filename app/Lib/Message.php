<?php
namespace App\Lib;


/**
 * APIメッセージクラス
 * Class Common
 */
class Message {

    const ERROR_REGISTER_TO_NO_LOGIN    = "会員登録が完了しました。ご登録いただいた情報でログインしてください";

    const ERROR_MAIL_DUPLICATE          = "ご入力いただいたメールアドレスは、既に登録されています。";
    const ERROR_REGISTER_TOKEN      = "URLが無効です。内容を再度ご確認ください。";
    const ERROR_MAIL_NONE           = "ご入力いただいたメールアドレスの会員情報がありません。";

    const ERROR_COUPON_ID_NONE      = "クーポン情報がありません。";
    const ERROR_COUPON_LIMIT_OVER   = "クーポンの利用期限が過ぎています。";

    const ERROR_SERVICE_ID_NONE     = "サービス券の情報がありません。";
    const ERROR_SERVICE_LIMIT_OVER  = "サービス券の利用期限が過ぎています。";

    const ERROR_CHARGE_PENALTY      = "現在退会すると違約金が発生いたします。";
    const ERROR_LOCATION            = "位置情報が取得できませんでした。";

    const ERROR_POINT_CHANGE_1      = "交換必要数に満たない為、交換できませんでした。";
    const ERROR_POINT_CHANGE_2      = "指定した%sを所持していない為、交換できませんでした。";
    const ERROR_POINT_CHANGE_3      = "交換上限に達している為、交換できませんでした。";

    const STAY_LOCATION_STORE_FIRST = "%sに来店しました。";
    const STAY_LOCATION_STORE       = "%sにて%s[%s %s]を獲得しました。";
    const STAY_LOCATION_GEOFENCE    = "%sにて%s[%s %s]を獲得しました。";

    const GOLD_GET_MESSAGE          = "veBeeスポットでゴールドコインを獲得できます";


    const ERROR_COIN_GET            = "コインの獲得に失敗しました";

}
