<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\ConfigService;

class ConfigController extends BaseApiController
{
    /**
     * システム管理コントローラー
     * Class ConfigController
     * @package App\Http\Controllers
     */
    public function __construct(ConfigService $mainService)
    {
        $this->mainService  = $mainService;
    }

    /**
     * 共通設定一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        try {
            // データを取得
            $data = $this->mainService->getKeyList();

            // ステータスOK
            return $this->success([
                'data'   => $data,
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * メンテナンス専用画面の表示フラグを返す
     */
    public function maintenance() {
        try {
            // ステータスOK
            return $this->success([
                'mtc_flg'   => $this->mainService->getKeyList()->mtc_flg,
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
}
