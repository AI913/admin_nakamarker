<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Model\UserPointsHistoryService;

class UserPointsHistoryController extends BaseAdminController
{
    protected $mainService;
    protected $userLocationService;

    /**
     * 顧客管理コントローラー
     * Class UserPointsHistoryController
     * @package App\Http\Controllers
     */
    public function __construct(UserPointsHistoryService $mainService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/user-points-history";
        $this->mainTitle    = 'ポイント履歴管理';
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
        if ($request->id) { $conditions['user_points_history.id'] = $request->id; }
        if ($request->name) { $conditions['user_points_history.name@like'] = $request->name; }
        
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['user' => []];
        
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }
}
