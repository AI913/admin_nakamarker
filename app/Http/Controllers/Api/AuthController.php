<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\UserService;
use App\Services\Api\ConfigService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Lib\Message;

class AuthController extends BaseApiController
{
    protected $mainService;
    protected $configService;

    /**
     * 認証管理コントローラー
     * Class AuthController
     * @package App\Http\Controllers
     */
    public function __construct(
        UserService $mainService,
        ConfigService $configService
    ) {
        $this->mainService  = $mainService;
        $this->configService = $configService;
    }

    /**
     * アプリ引き継ぎ時の認証処理
     */
    public function login(Request $request) {
        try {
            // データを配列化
            $conditions['name'] = $request->input('name');
            $conditions['onetime_password'] = $request->input('password');

            // データを保存
            if($this->mainService->searchExists($conditions)) {
                // ユーザ情報を取得
                $user = $this->mainService->searchOne($conditions);
                // ステータスOK
                return $this->success(['user_token' => $user->user_token]);
            }
            return $this->error(-9, ["message" => __FUNCTION__.":".Message::ERROR_LOGIN_FAILURE]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * ワンタイムパスワード発行処理
     * ※期限は1週間単位で設定する
     */
    public function password(Request $request) {
        try {
            \DB::beginTransaction();
            // パスワードをリターン
            $password = $this->mainService->issueOnetimePassword();
            
            // 発行したパスワードデータを保存(有効期限は共通設定テーブルから値を抽出)
            $data = [
                'id'               => Auth::user()->id, // ユーザID
                'onetime_password' => $password,
                'limit_date'       => Carbon::now()->addWeek($this->configService->searchOne(['key' => 'password_limit_date'])->value),
            ];
            // ユーザデータを更新
            $user = $this->mainService->save($data);

            // アプリ表示用にカスタマイズ
            $confirmPassword = str_split($password, 4);
            $confirmPassword = $confirmPassword[0].'-'.$confirmPassword[1].'-'.$confirmPassword[2];
    
            \DB::commit();
            // ステータスOK
            return $this->success([
                'password' => $confirmPassword,
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * Firebaseログイン処理(電話番号認証)
     * ※アプリ側からfirebase_uidを送信するため、
     * 　ここではユーザデータの更新処理の記載のみとなる
     */
    public function phoneRegister(Request $request) {
        try {
            \DB::beginTransaction();
            // データを配列化
            $data = $request->all();

            // データを保存
           $this->mainService->save($data);
    
            \DB::commit();
            // ステータスOK
            return $this->success();
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * ユーザの退会処理
     */
    public function unsubscribe() {
        try {
            // statusを退会済みアカウントの値にセット
            $data['status'] = config('const.user_app_unsubscribe');
            $data['id'] = Auth::user()->id;

            // データを保存
            $this->mainService->save($data);
            // ステータスOK
            return $this->success(["message" => Message::SUCCESS_UNSUBSCRIBE]);
            
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
}
