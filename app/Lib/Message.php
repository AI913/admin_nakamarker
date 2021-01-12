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

}
