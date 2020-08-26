<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        if ($request->status) { $conditions['community_history.status'] = $request->status; }
        
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = [];
        
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }
}
