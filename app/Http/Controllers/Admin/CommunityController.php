<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\CommunityService;
use App\Services\Model\UserService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

class CommunityController extends BaseAdminController
{
    protected $mainService;

    public function __construct(CommunityService $mainService)
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/community";
        $this->mainTitle    = 'コミュニティ管理';
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

    /**
     * モーダルに必要なデータを取得
     * @param $user_id
     * @return array
     */
    public function detail($id) {

        // 詳細(Modal)のDataTable
        // 〇検索条件
        $conditions = [];
        $conditions['id'] = $id;
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = [];
        $data = $this->mainService->searchOne($conditions, $sort, $relations);
        
        return [
            'status' => 1,
            'data' => $data,
        ]; 
    }

    /**
     * ユーザ情報取得
     * @param $id
     * @throws \Exception
     */
    public function community_users($id, UserService $userService) {
        
        // コミュニティに紐づくユーザ情報を取得
        return DataTables::eloquent($userService->isCommunityUserData($id))->make();
    }
}
