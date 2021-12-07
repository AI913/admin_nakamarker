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
        // メール送信
        Mail::to(env('SUPPORT_MAIL_ADDRESS'))->send( new SendMail( (object)[
            'email'         => $request->email,
            'user_id'       => $request->user_id,
            'user_name'     => $request->user_name,
            'contact_body'  => $request->contact_body
        ]) );

        return 1;
    }
}
