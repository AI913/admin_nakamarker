<?php

/**
 * プッシュ通知クラス
 * Class Notification
 */
class Notification
{
    /**
     * 設定されているユーザートークン配列に対してメッセージ送信
     * @param $push_tokens
     * @param $message
     * @return bool|string
     */
    public static function sendUserMessage($push_tokens, $message) {
        $success = 0; $failure = 0;
        if (count($push_tokens) >= 1000) {
            $idx = 0; $push_list = [];
            foreach($push_tokens as $value) {
                array_push($push_list, $value);
                $idx++;
                // 1000件毎に送信
                if ($idx >= 1000) {
                    $result = self::send($push_list, $message);
                    $success+= $result->success;
                    $failure+= $result->failure;
                    $idx = 0; $push_list = [];
                }
            }
        } else {
            // 1000件未満は1回で送信
            $result = self::send($push_tokens, $message);
            $success = $result->success;
            $failure = $result->failure;
        }

        Log::debug("[send_message] success:".$success.", failure:".$failure);
    }
    /**
     * 送信処理
     * @param $push_tokens
     * @param $message
     * @return bool|string
     */
    public static function send($push_tokens, $message) {
        $headers = [
            "Authorization: key=" . env('FIREBASE_API_KEY'),
            "Content-Type: application/json"
        ];
        $fields = [
            "registration_ids" => is_array($push_tokens) ? $push_tokens : [$push_tokens],
            "notification" => [
                "text" => $message
            ]
        ];

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, env('FIREBASE_API_URL'));
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($handle);
        curl_close($handle);
        return $result;
    }
}