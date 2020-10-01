<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\PushHistoryService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

class PushHistoryController extends BaseAdminController
{
    /**
     * 通知機能管理コントローラー
     * Class PushHistoryController
     * @package App\Http\Controllers
     */
    public function __construct(PushHistoryService $mainService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/push-history";
        $this->mainTitle    = 'プッシュ通知履歴管理';
    }

    /**
     * メインリストデータ取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function main_list(Request $request) {
        // 〇検索条件
        $conditions = [];
        $conditions['del_flg'] = 0;
        if ($request->id) { $conditions['push_histories.id'] = $request->id; }
        if ($request->title) { $conditions['push_histories.title@like'] = $request->title; }
        if ($request->type) { $conditions['push_histories.type'] = $request->type; }
        // statusのリクエストがあり、かつリクエストが数値の場合に検索条件の値をセットする
        if ($request->has('status') && is_numeric($request->status)) { $conditions['push_histories.status'] = $request->status; }
        
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = [];
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }


    /**
     * プッシュ通知管理一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // ステータスリスト追加
        return parent::index()->with([
            'type_list' => Common::getPushTypeList(),
            'status_list' => Common::getPushStatusList(),
        ]);
    }

}
