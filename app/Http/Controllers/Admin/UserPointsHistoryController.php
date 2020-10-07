<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Model\UserPointsHistoryService;
use App\Services\Model\PointsGiftHistoryService;

class UserPointsHistoryController extends BaseAdminController
{
    protected $mainService;
    protected $pointsGiftHistoryService;

    /**
     * 顧客管理コントローラー
     * Class UserPointsHistoryController
     * @package App\Http\Controllers
     */
    public function __construct(UserPointsHistoryService $mainService, PointsGiftHistoryService $pointsGiftHistoryService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/user-points-history";
        $this->mainTitle    = 'ポイント履歴管理';

        $this->pointsGiftHistoryService = $pointsGiftHistoryService;
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
        if ($request->type) { $conditions['user_points_histories.type'] = $request->type; }
        if ($request->charge_flg) { $conditions['user_points_histories.charge_flg'] = $request->charge_flg; }
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
        // 検索用リスト追加
        return parent::index()->with([
            'type_list'    => Common::getPointStatusList(),
            'charge_list'  => Common::getPointChargeFlagList(),
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
        $relations = ['user' => []];
        $data = $this->mainService->searchOne($conditions, $sort, $relations);
        
        return [
            'status' => 1,
            'data' => $data,
        ]; 
    }

    /**
     * 特定ユーザの履歴を取得
     * @param $id
     * @throws \Exception
     */
    public function point_histories($user_id) {
        
        // 詳細(Modal)のDataTable
        // 〇検索条件
        $conditions = [];
        $conditions['user_id'] = $user_id;
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['user' => []];

        // コミュニティに紐づく申請状況の履歴を取得
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }

    /**
     * ユーザーポイント履歴の作成・編集処理
     * @param Request $request
     * @param UserPointService $userPointService
     * @return array
     * @throws \Exception
     */
    public function updatePoints(Request $request) {

        if (!$request->user_id) {
            return ['status' => -1];
        }

        // 保存データを配列に格納
        $data = [
            'type'              => $request->type,
            'give_point'        => $request->give_point,
            'pay_point'         => 0,
            'charge_flg'        => $request->charge_flg,
            'user_id'           => $request->user_id,
            'update_user_id'    => \Auth::user()->id,
        ];

        if(!empty($request->id)) {
            $data['id'] = $request->id;
        }
        $model = $this->mainService->save($data);
        // ポイント履歴の更新or作成
        if($model) {
            
            $data['user_points_history_id'] = $model->id;
            $input = $this->pointsGiftHistoryService->saveBefore($data);
            $this->pointsGiftHistoryService->save($input);

            return [
                'status' => 1,
                'id' => $request->user_id 
            ];
        }
        return ['status' => -1];
    }
}
