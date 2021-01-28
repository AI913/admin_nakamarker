<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Config;
use App\Lib\Message;
use App\Lib\Common;

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
        try {
            // user_tokenの取得
            $token = $request->bearerToken();
    
            // user_tokenの確認 & ユーザの取得
            $user = User::where('user_token', '=', $token)->first();
            
            // ログイン条件を満たしているか確認
            // 1. ユーザが存在していること
            // 2. ユーザの会員ステータスが(一般)会員ユーザーもしくは(管理者)運営管理者であること
            // 3. 削除フラグが立っていないこと
            if ($user && $user->del_flg === 0) {
                if ($user->status === config('const.user_app_member') || $user->status === config('const.user_admin_system')) {
                    // ログインがまだの場合はログインを実行
                    if(!Auth::user()) {
                        Auth::login($user);
                    }
                    // ログイン日時の保存チェック
                    $reset_hour = Config::where('key', '=', 'reset_hour')->first();
                    $reset_interval = Config::where('key', '=', 'reset_interval')->first();
                    if(Common::getLoginFlg($user->login_time, $reset_hour->value, $reset_interval->value)) {
                        // 保存データのセット
                        $data = [
                            'id'            => $user->id,
                            'login_time'    => Common::getLoginFlg($user->login_time, $reset_hour->value, $reset_interval->value)
                        ];

                        // データの保存
                        User::find($user->id)->fill($data)->save();
                    }
                    return $next($request);
                }
            }

            // 認証失敗時
            return response()->json([
                "status"  => -1,
                "message" => Message::ERROR_NO_LOGIN
            ]);
        } catch (\Exception $e) {
            // エラー内容をログに記録
            \Log::error($e->getMessage());
            // エラーをリターン
            return response()->json([
                "status"  => -9,
                "message" => __FUNCTION__.":".$e->getMessage()
            ]);
        }
    }
}
