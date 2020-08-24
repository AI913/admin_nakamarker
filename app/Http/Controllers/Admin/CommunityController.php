<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\CommunityService;
use App\Services\Model\UserService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

class CommunityController extends BaseAdminController
{
    protected $userService;

    public function __construct(CommunityService $mainService, UserService $userService)
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/community";
        $this->mainTitle    = 'コミュニティ管理';
        $this->userService = $userService;
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
        if ($request->id) { $conditions['communities.id'] = $request->id; }
        if ($request->name) { $conditions['communities.name@like'] = $request->name; }
        // statusのリクエストがあり、かつリクエストが数値の場合に検索条件の値をセットする
        if ($request->has('status') && is_numeric($request->status)) { $conditions['communities.status'] = $request->status; }
        
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = [];
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }

    public function index()
    {
        // ステータスリスト追加
        return parent::index()->with([
            'status_list' => Common::getOpenStatusList(),
        ]);
    }
}
