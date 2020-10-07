<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\ConfigService;
use App\Services\Model\UserService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class ConfigController extends BaseAdminController
{

    protected $userService;

    /**
     * システム管理コントローラー
     * Class ConfigController
     * @package App\Http\Controllers
     */
    public function __construct(ConfigService $mainService, UserService $userService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/config";
        $this->mainTitle    = '共通設定管理';

        $this->userService = $userService;
    }

    /**
     * メインリストデータ取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function main_list(Request $request) {
        // 最終更新者のユーザ名をユーザIDに変換
        if ($request->update_user_name) { 
            $user = $this->userService->searchOne(['name@like' => $request->update_user_name]);
        }

        // 〇検索条件
        $conditions = [];
        // ID、キー
        if ($request->id) { $conditions['configs.id'] = $request->id; }
        if ($request->key) { $conditions['configs.key'] = $request->key; }
        if ($request->update_user_name) { $conditions['configs.update_user_id'] = $user->id; }
        // if ($request->update_user_name) { $conditions['update_user.name'] = $request->update_user_name; }
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['update_user' => []];
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }

    /**
     * バリデーション設定
     * @param Request $request
     * @return array
     */
    public function validation_rules(Request $request)
    {
        // バリデーションチェック
        return [
            'key'  => [Rule::unique('configs')->ignore($request['id'], 'id')->where('del_flg', '=', 0)],
        ];
    }

    /**
     * バリデーションメッセージ
     * @param Request $request
     * @return array
     */
    public function validation_message(Request $request) {
        return [
            'key.unique'        => 'このキーはすでに使用されています',
        ];
    }

}
