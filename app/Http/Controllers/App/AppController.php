<?php

namespace App\Http\Controllers\App;

use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;

class AppController extends BaseController
{

    /**
     * 問い合わせメール送信
     * @param Request $request
     * @return int
     */
    public function send(Request $request) {
        // TODO　送信プログラムでエラーが出るので、一旦send_mail関数で送信する
        $to = env('SUPPORT_MAIL_ADDRESS');
        $subject = "[ナカマーカー]問い合わせ";
        $message = "問い合わせがありました";
        $message.= "メールアドレス：".$request->email."\r\n";
        $message.= "ユーザーID：".$request->user_id."\r\n";
        $message.= "ユーザー名：".$request->user_name."\r\n";
        $message.= "お問い合わせ内容：\r\n".$request->contact_body."\r\n";
        $headers = "From: ".env('MAIL_FROM_ADDRESS');
        mb_send_mail($to, $subject, $message, $headers, '-f' . env('MAIL_FROM_ADDRESS'));

//        // メール送信
//        Mail::send( new SendMail( (object)[
//            'email'         => $request->email,
//            'user_id'       => $request->user_id,
//            'user_name'     => $request->user_name,
//            'contact_body'  => $request->contact_body
//        ]) );

        return 1;
    }
}
