<?php
namespace App\Lib;


/**
 * APIメッセージクラス
 * Class Common
 */
class Message {

    const SUCCESS_UNSUBSCRIBE       = "退会処理が完了しました";
  
    const ERROR_NO_LOGIN                = "ユーザ情報が認識できません";
    const ERROR_LOGIN_FAILURE           = "ユーザ名もしくはパスワードが違います";
    const ERROR_REGISTER_TOKEN          = "トークンが無効です";
    const ERROR_NOT_HOST                = "ホスト権限がありません";
    const ERROR_NOT_COMMUNIRY_MEMBER    = "コミュニティに加盟していないため、権限がありません";
    const ERROR_NOT_MARKER_DUPLICATE    = "同じマーカーを複数個登録することは出来ません";
    const ERROR_NOT_OVER_FREE_POINT     = "無料ポイントが不足しています";
    const ERROR_NOT_OVER_CHARGE_POINT   = "有料ポイントが不足しています";
    const ERROR_NOT_EXISTS_USER         = "ユーザが存在しません";
}
