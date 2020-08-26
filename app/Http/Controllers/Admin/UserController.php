<?php

namespace App\Http\Controllers\Admin;

use App\Lib\Common;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use App\Services\Model\UserService;
use App\Services\Model\UserLocationService;
use App\Services\Model\CommunityService;
use App\Services\Model\ConfigService;

class UserController extends BaseAdminController
{
    protected $mainService;

    /**
     * 顧客管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(UserService $mainService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/user";
        $this->mainTitle    = 'ユーザ管理';
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
        
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = [];
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }

    /**
     * 顧客管理一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // dd($this->mainService->isUserLocationData(3));
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
    public function user_locations($id, UserLocationService $userLocationService) {
        
        // ユーザの登録場所とそれに紐づくマーカー情報を取得
        return DataTables::eloquent($userLocationService->isUserLocationData($id))->make();
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
     * ユーザーポイント履歴取得(未実装)
     * @param $id
     * @param $type
     * @param $used
     * @param UserPointService $userPointService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
//     public function user_point($id, $type, $used, UserPointService $userPointService) {
// //        // 〇検索条件
// //        $conditions = [];
// //        $conditions['user_points.user_id'] = $id;
// //        // 〇ソート条件
// //        $sort = [];
// //        $sort['user_points.id'] = 'desc';
// //        // 〇リレーション
// //        $relations = ['location_store' => [], 'location_geofence' => []];

//         $conditions['not_receive_flg'] = true;
//         // 種別
//         if ($type) {
//             $conditions['point_type'] = $type;
//         }
//         // 未使用
//         if ($used) {
//             $conditions['used'] = true;
//             $conditions['limit_date'] = true;
//         }

//         return DataTables::eloquent($userPointService->getUserPointHistoryQuery($id, $conditions))->make();
//     }

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
