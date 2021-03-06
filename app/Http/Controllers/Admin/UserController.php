<?php

namespace App\Http\Controllers\Admin;

use App\Lib\Common;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use App\Services\Model\UserService;
use App\Services\Model\UserLocationService;
use App\Services\Model\UserPointsHistoryService;
use App\Services\Model\UserMarkerService;
use App\Services\Model\MarkerService;
use App\Services\Model\CommunityService;
use App\Services\Model\CommunityHistoryService;
use App\Services\Model\ConfigService;

use Illuminate\Validation\Rule;
class UserController extends BaseAdminController
{
    protected $mainService;
    protected $userPointHistoryService;
    protected $userLocationService;
    protected $userMarkerService;
    protected $markerService;

    /**
     * 顧客管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(
        UserService $mainService,
        UserPointsHistoryService $userPointHistoryService,
        UserLocationService $userLocationService,
        UserMarkerService $userMarkerService,
        MarkerService $markerService
    )
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/user";
        $this->mainTitle    = 'ユーザ管理';

        $this->userPointHistoryService = $userPointHistoryService;
        $this->userLocationService = $userLocationService;
        $this->userMarkerService = $userMarkerService;
        $this->markerService = $markerService;
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
        $conditions['del_flg'] = 0;
        if ($request->id) { $conditions['users.id'] = $request->id; }
        if ($request->name) { $conditions['users.name@like'] = $request->name; }
        if ($request->user_unique_id) { $conditions['users.user_unique_id@like'] = $request->user_unique_id; }
        if ($request->status) { $conditions['users.status'] = $request->status; }

        return DataTables::eloquent($this->mainService->getUserPointQuery($conditions))->make();
    }

    /**
     * 顧客管理一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
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
        $conditions['del_flg'] = 0;
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
        return DataTables::eloquent($this->userLocationService->getUserLocationQuery($id))->make();
    }

    /**
     * 登録場所情報の詳細を取得
     * @param $id
     * @throws \Exception
     */
    public function user_locations_detail($user_id, $location_id) {
        // ユーザの登録場所とそれに紐づくマーカーの詳細情報を取得
        $data = $this->userLocationService->getUserLocationQuery($user_id, $location_id)->first();

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
     * 所有するマーカー情報を取得
     * @param $id
     * @throws \Exception
     */
    public function user_markers($user_id) {
        // ユーザの所有マーカー情報を取得
        return DataTables::eloquent($this->markerService->getUserMarkerQuery($user_id))->make();
    }

    /**
     * ユーザコミュニティ情報取得
     * @param $id
     * @throws \Exception
     */
    public function user_communities($id, CommunityService $communityService) {

        // ユーザに紐づいているコミュニティを取得
        return DataTables::eloquent($communityService->getUserCommunityQuery($id))->make();
    }

    /**
     * 特定ユーザのポイント履歴を取得
     * @param $id
     * @throws \Exception
     */
    public function point_histories($id) {

        // 詳細(Modal)のDataTable
        // 〇検索条件
        $conditions = [];
        $conditions['to_user_id'] = $id;
        $conditions['del_flg'] = 0;
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
        // 選択したユーザの総ポイント数を取得
        $points = $this->mainService->getUserPointQuery(['users.id' => \Auth::user()->id])->first();

        // ポイント付与の種別がギフトかつ無料の付与ポイントが無料の所持ポイントより多い場合
        if($request->type == 2 && $request->charge_type == 1 && $request->give_point > $points->free_total_points) {
            return ['status' => -2];
        }
        // ポイント付与の種別がギフトかつ有料の付与ポイントが有料の所持ポイントより多い場合
        if($request->type == 2 && $request->charge_type == 2 && $request->give_point > $points->total_points) {
            return ['status' => -2];
        }

        // 保存データを配列に格納
        $data = [
            'type'              => $request->type,
            'give_point'        => $request->give_point,
            'pay_point'         => 0,
            'charge_type'        => $request->charge_type,
            'to_user_id'        => $request->user_id,
            'from_user_id'      => $request->type == 2 ? \Auth::user()->id : null,
            'status'            => 2,
            'update_user_id'    => \Auth::user()->id,
        ];

        // ポイント履歴の更新or作成
        if($model = $this->userPointHistoryService->save($data)) {
            // ポイント付与の種別がギフトだった場合
            if($model->type == 2) {
                // ポイントをギフトしたユーザのポイントを消費
                $this->userPointHistoryService->getPayPointQuery($data['from_user_id'], $data['give_point'], $data['charge_type']);
            }

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

        if ($request->password) {
            // パスワードのハッシュ化
            $request['password'] = Common::getEncryptionPassword($request->password);
        }

        // 除外項目
        $input = $request->except($this->except());

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
        return redirect(route($this->mainRoot))->with('info_message', $this->mainTitle.'情報を削除しました');
    }

    /**
     * ユーザーロケーション論理削除
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Exception
     */
    public function removeLocation(Request $request) {
        $this->userLocationService->remove($request->id);
        return redirect(route($this->mainRoot))->with('info_message', $this->mainTitle.'情報を削除しました');
    }

    /**
     * ユーザーの所有マーカー論理削除
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Exception
     */
    public function removeMarker(Request $request) {
        $this->userMarkerService->remove($request->id);
        return redirect(route($this->mainRoot))->with('info_message', $this->mainTitle.'情報を削除しました');
    }

    /**
     * ユーザーのポイント履歴論理削除
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Exception
     */
    public function removePoint(Request $request) {
        $this->userPointHistoryService->remove($request->id);
        return redirect(route($this->mainRoot))->with('info_message', $this->mainTitle.'情報を削除しました');
    }

    /**
     * バリデーション設定
     * @param Request $request
     * @return array
     */
    public function validation_rules(Request $request)
    {
        if ($this->isCreate($request)) {
            // 作成時のバリデーションチェック
            return [
                'name'     => [Rule::unique('users')->ignore($request['id'], 'id')->where('del_flg', '=', 0)],
                'email'    => ['email', 'max:100', 'regex:/^[a-zA-Z0-9\.\-@]+$/', Rule::unique('users')->ignore($request['id'], 'id')->where('del_flg', '=', 0)],
                'password' => ['min:6', 'regex:/^[0-9a-zA-Z\_@!?#%&]+$/'],
            ];
        } else {
            // 編集時のバリデーションチェック
            return [
                'name'     => [Rule::unique('users')->ignore($request['id'], 'id')->where('del_flg', '=', 0)],
                'email' => ['email', 'max:100', 'regex:/^[a-zA-Z0-9\.\-@]+$/', Rule::unique('users')->ignore($request['id'], 'id')->where('del_flg', '=', 0)],
            ];
        }
    }

    /**
     * バリデーションメッセージ
     * @param Request $request
     * @return array
     */
    public function validation_message(Request $request) {
        return [
            'name.unique'    => 'このユーザ名はすでに使用されています',

            'email.email'    => 'メールアドレスの形式で入力してください',
            'email.max'      => 'メールアドレスは100文字以下で登録してください',
            'email.unique'   => 'このメールアドレスはすでに使用されています',
            'email.regex'    => '@以前は半角英数字で入力してください',

            'password.min'      => 'パスワードは6文字以上で登録してください',
            'password.regex'    => 'パスワードは半角英数字及び「_@!?#%&」の記号のみで入力してください',
        ];
    }

    // ポイント削除機能(削除予定)
    public function pay_points(Request $request) {
        if (!$request->user_id) {
            return ['status' => -1];
        }

        if($this->userPointHistoryService->getPayPointQuery($request->user_id, $request->pay_point, $request->charge_type)){
            return [
                'status' => 1,
                'id' => $request->user_id
            ];
        }
        return ['status' => -2];
    }
}
