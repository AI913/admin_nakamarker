<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\UserService;
use App\Services\Api\ConfigService;
use Carbon\Carbon;

class UserController extends BaseApiController
{
    protected $mainService;
    protected $configService;

    /**
     * ユーザ管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(UserService $mainService, ConfigService $configService) {
        $this->mainService  = $mainService;
        $this->configService = $configService;
    }

    /**
     * ユーザ一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function index() {
    //     
    // }

    /**
     * ユーザ作成
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        try {
            \DB::beginTransaction();

            // ユーザ名の登録
            $user = $this->mainService->save($request->all(), true, false);

            // ユーザトークンの発行
            $token = $this->mainService->issueUserToken($user->id);

            // ユーザ情報更新
            $data = [
                'id'            => $user->id,
                'user_token'    => $token
            ];
            $user = $this->mainService->save($data, false, false);

            \DB::commit();

            // ステータスOK
            return $this->success(['user_token' => $user->user_token]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * Firebaseログイン処理
     */
    public function register(Request $request) {

        try {
            // データを配列化
            $data = $request->all();

            // データを保存
            $user = $this->mainService->save($data);
    
            // ステータスOK
            return $this->success(['uid' => $user->firebase_uid]);
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
            // パスワードをリターン
            $password = $this->mainService->issueOnetimePassword();
            
            // 発行したパスワードデータを保存(有効期限は共通設定テーブルから値を抽出)
            $data = [
                'id'               => $request->input('id'), // ユーザID
                'onetime_password' => $password,
                'limit_date'       => Carbon::now()->addWeek($this->configService->searchOne(['key' => 'password_limit_date'])->value),
            ];
            // ユーザデータを更新
            $user = $this->mainService->save($data);

            // アプリ表示用にカスタマイズ
            $confirmPassword = str_split($password, 4);
            $confirmPassword = $confirmPassword[0].'-'.$confirmPassword[1].'-'.$confirmPassword[2];
    
            // ステータスOK
            return $this->success([
                'password' => $confirmPassword,
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * アプリ引き継ぎ時の認証処理
     */
    public function login(Request $request) {
        try {
            // データを配列化
            $conditions = $request->all();

            // データを保存
            if($this->mainService->searchExists($conditions)) {
                // ユーザ情報を取得
                $user = $this->mainService->searchOne($conditions);
                // ステータスOK
                return $this->success(['user_token' => $user->user_token]);
            }
            return $this->error(-9, ["message" => __FUNCTION__.":ユーザ名もしくはパスワードが違います"]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
}
