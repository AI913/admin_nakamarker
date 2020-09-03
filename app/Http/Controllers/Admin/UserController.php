<?php

namespace App\Http\Controllers\Admin;

use App\Lib\Common;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use App\Services\Model\UserService;
use App\Services\Model\UserLocationService;
use App\Services\Model\UserPointsHistoryService;
use App\Services\Model\CommunityService;
use App\Services\Model\CommunityHistoryService;
use App\Services\Model\ConfigService;

class UserController extends BaseAdminController
{
    protected $mainService;
    protected $userPointHistoryService;
    protected $userLocationService;

    /**
     * 顧客管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(
        UserService $mainService, 
        UserPointsHistoryService $userPointHistoryService,
        UserLocationService $userLocationService
    ) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/user";
        $this->mainTitle    = 'ユーザ管理';

        $this->userPointHistoryService = $userPointHistoryService;
        $this->userLocationService = $userLocationService;
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
        if ($request->id) { $conditions['users.id'] = $request->id; }
        if ($request->name) { $conditions['users.name@like'] = $request->name; }
        if ($request->email) { $conditions['users.email@like'] = $request->email; }
        if ($request->status) { $conditions['users.status'] = $request->status; }
        
        return DataTables::eloquent($this->mainService->isUserPointData($conditions))->make();
    }

    /**
     * 顧客管理一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // dd($this->mainService->isUserPointData()->get());
        // ステータスリスト追加
        return parent::index()->with(
            ['status_list' => Common::getUserStatusList()]
        );
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
     * 登録場所情報の取得
     * @param $id
     * @throws \Exception
     */
    public function user_locations($id) {
        
        // ユーザの登録場所とそれに紐づくマーカー情報を取得
        return DataTables::eloquent($this->userLocationService->isUserLocationData($id))->make();
    }

    /**
     * 登録場所情報の詳細を取得
     * @param $id
     * @throws \Exception
     */
    public function user_locations_detail($user_id, $location_id) {
        // ユーザの登録場所とそれに紐づくマーカーの詳細情報を取得
        $data = $this->userLocationService->isUserLocationData($user_id, $location_id)->first();

        // マーカー用の画像URLを配列に格納
        $data['marker_image_url'] = Common::getImageUrl($data->marker_image);
        
        return [
            'status' => 1,
            'data' => $data,
        ]; 
    }

    /**
     * 登録場所の画像削除
     * @param $id
     * @throws \Exception
     */
    public function user_locationImage_delete(Request $request) {
        // 登録場所に紐づく画像情報を強制削除
        return ($this->userLocationService->deleteImage($request->location_id));
    }

    /**
     * ユーザコミュニティ情報取得
     * @param $id
     * @throws \Exception
     */
    public function user_communities($id, CommunityService $communityService) {
        
        // ユーザに紐づいているコミュニティを取得
        return DataTables::eloquent($communityService->isUserCommunityData($id))->make();
    }

    /**
     * 特定ユーザの履歴を取得
     * @param $id
     * @throws \Exception
     */
    public function point_histories($id) {
        
        // 詳細(Modal)のDataTable
        // 〇検索条件
        $conditions = [];
        $conditions['user_id'] = $id;
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['user' => []];

        // コミュニティに紐づく申請状況の履歴を取得
        return DataTables::eloquent($this->userPointHistoryService->searchQuery($conditions, $sort, $relations))->make();
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

        // ポイント履歴の更新or作成
        if($this->userPointHistoryService->save($data)) {
            return [
                'status' => 1,
                'id' => $request->user_id 
            ];
        }
        return ['status' => -1];
    }

    /**
     * 保存前処理
     * @param Request $request
     * @return array
     */
    public function saveBefore(Request $request) {
        // 保存処理モード
        $register_mode = $request->register_mode;
        // 除外項目
        $input = $request->except($this->except());
        // パスワードあり
        if ($request->password) {
            // パスワードのハッシュ化
            $input['password'] = Common::getEncryptionPassword($request->password);
        }
        // 編集時にパスワードがない場合
        if (!$request->password && $register_mode == 'edit') {
            // 配列の要素からpasswordを消去する
            unset($input['password']);
        }

        return $input;
    }

    /**
     * ユーザー論理削除
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Exception
     */
    public function remove(Request $request) {
        $this->mainService->removeUserEmail($this->mainService->remove($request->id));
        return redirect($this->mainRoot)->with('info_message', $this->mainTitle.'情報を削除しました');
    }
}
