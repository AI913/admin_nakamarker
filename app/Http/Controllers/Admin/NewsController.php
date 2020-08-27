<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\NewsService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

class NewsController extends BaseAdminController
{
    /**
     * 情報管理コントローラー
     * Class NewsController
     * @package App\Http\Controllers
     */
    public function __construct(NewsService $mainService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/news";
        $this->mainTitle    = 'お知らせ管理';
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
        if ($request->id) { $conditions['news.id'] = $request->id; }
        if ($request->title) { $conditions['news.title'] = $request->title; }
        if ($request->type) { $conditions['news.type'] = $request->type; }
        // statusのリクエストがあり、かつリクエストが数値の場合に検索条件の値をセットする
        if ($request->has('status') && is_numeric($request->status)) { $conditions['news.status'] = $request->status; }
        
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['user' => []];
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }


    /**
     * ニュース管理一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // ステータスリスト追加
        return parent::index()->with([
            'type_list' => Common::getNewsTypeList(),
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
}
