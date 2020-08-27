<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\CommunityHistoryService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

class CommunityHistoryController extends BaseAdminController
{
    protected $mainService;
    
    /**
     * コミュニティ履歴管理コントローラー
     * Class CommunityHistoryController
     * @package App\Http\Controllers
     */
    public function __construct(CommunityHistoryService $mainService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/community-history";
        $this->mainTitle    = 'コミュニティ履歴管理';
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
        if ($request->id) { $conditions['community_history.id'] = $request->id; }
        if ($request->community_name) { $conditions['community_history.community_name@like'] = $request->community_name; }
        if ($request->user_name) { $conditions['community_history.user_name@like'] = $request->user_name; }
        if ($request->status) { $conditions['community_history.status'] = $request->status; }
        
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['user' => [], 'community' => []];
        
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }

    /**
     * コミュニティ履歴管理一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // ステータスリスト追加
        return parent::index()->with(
            ['status_list' => Common::getEntryStatusList()]
        );
    }
}
