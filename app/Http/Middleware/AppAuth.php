<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class AppAuth
{
    /**
     * アプリからの認証処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // user_tokenの取得
        $token = $request->bearerToken();

        // 認証
        if (DB::table('users')->where('id', '=', $request->input('id'))->where('user_token', '=', $token)->first()) {
            return $next($request);
        }
        // エラーをリターン
        return response()->json([
            "status"  => -9,
            "message" => "ユーザ情報が認識できません"
        ]);
    }
}
