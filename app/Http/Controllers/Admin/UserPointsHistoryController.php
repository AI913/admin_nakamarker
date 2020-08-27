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
        if ($request->id) { $conditions['user_points_histories.id'] = $request->id; }
        if ($request->name) { $conditions['user_points_histories.user.name@like'] = $request->name; }
        
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['user' => []];
        
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }

    /**
     * ポイント履歴管理一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // 〇検索条件
        // $conditions = [];
        // // if ($request->id) { $conditions['user_points_histories.id'] = $request->id; }
        // // if ($request->name) { $conditions['users.name@like'] = $request->name; }
        
        // // 〇ソート条件
        // $sort = [];
        // // 〇リレーション
        // $relations = ['user' => []];
        
        // dd($this->mainService->searchQuery($conditions, $sort, $relations)->get());

        // ステータスリスト追加
        return parent::index()->with(
            ['status_list' => Common::getPointStatusList()]
        );
    }
}
