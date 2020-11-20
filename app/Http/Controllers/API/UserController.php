<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\UserService;

class UserController extends BaseApiController
{
    protected $mainService;

    /**
     * ユーザ管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(UserService $mainService) {
        $this->mainService  = $mainService;
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
        // ステータスリスト追加
        return parent::index()->with(
            ['status_list' => Common::getUserStatusList()]
        );
    }

}
