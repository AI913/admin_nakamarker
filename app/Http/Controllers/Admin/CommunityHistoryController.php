<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\CommunityHistoryService;
use App\Services\Model\CommunityService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

class CommunityHistoryController extends BaseAdminController
{
    protected $mainService;
    protected $communityService;
    
    /**
     * コミュニティ履歴管理コントローラー
     * Class CommunityHistoryController
     * @package App\Http\Controllers
     */
    public function __construct(CommunityHistoryService $mainService, CommunityService $communityService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/community-history";
        $this->mainTitle    = 'コミュニティ履歴管理';

        $this->communityService = $communityService;
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

    /**
     * 申請状況の編集処理
     * 引数：
     */
    public function updateStatus(Request $request) {
        return $this->mainService->updateStatus($request);
    }

    /**
     * モーダルに必要なデータを取得
     * @param $user_id
     * @return array
     */
    public function detail($id) {

        $data = $this->mainService->get_detail($id)->first();

        // 公開設定の値を取得
        if ($data->open_flg === config('const.private')) {
            $data['open_flg_name'] = config('const.private_name');
        } else {
            $data['open_flg_name'] = config('const.open_name');
        }
        
        return [
            'status' => 1,
            'data' => $data,
        ]; 
    }

    /**
     * 申請状況の履歴を取得
     * @param $id
     * @throws \Exception
     */
    public function entry_histories($id) {
        
        // 選択した申請状況のデータを取得
        $data = $this->mainService->searchOne(['id' => $id]);
        // 詳細(Modal)のDataTable
        // 〇検索条件
        $conditions = [];
        $conditions['community_id'] = $data->community_id;
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['user' => [], 'community' => []];

        // コミュニティに紐づく申請状況の履歴を取得
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }

    /**
     * 保存前処理
     * @param Request $request
     * @return array
     * @throws \Exception
     * $request->image_file : inputタイプのhidden属性
     * $request->file('upload_image') : inputタイプのfile属性
     */
    public function saveBefore(Request $request) {
        
        // 除外項目
        $input = $request->except($this->except());
        
        // コミュニティのメンバー追加処理
        if($request->status === 2) {
            // 現時点でのコミュニティに登録されている人数に加算
            $member_update = $this->mainService->member_update($request->community_id)->count() + 1;
            // communitiesテーブルのmemberカラムを更新
            $this->communityService->member_save($member_update);
        }

        return $input;
    }

}
