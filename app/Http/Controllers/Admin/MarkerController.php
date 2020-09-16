<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\MarkerService;
use App\Services\Model\UserService;
use App\Services\Model\UserMarkerService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

class MarkerController extends BaseAdminController
{
    /**
     * マーカー管理コントローラー
     * Class MarkerController
     * @package App\Http\Controllers
     */
    public function __construct(MarkerService $mainService, UserMarkerService $userMarkerService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/marker";
        $this->mainTitle    = 'マーカー管理';

        $this->userMarkerService = $userMarkerService;
    }

    /**
     * 登録項目除外配列(下記以外に不要な項目があればオーバーライド)
     * @return array
     */
    public function except() {
        return ["_token", "register_mode", "upload_image", "img_delete", "delete_flg_on", 'image_flg'];
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
        if ($request->id) { $conditions['markers.id'] = $request->id; }
        if ($request->type) { $conditions['markers.type'] = $request->type; }
        if ($request->name) { $conditions['markers.name@like'] = $request->name; }
        // statusのリクエストがあり、かつリクエストが数値の場合に検索条件の値をセットする
        if ($request->has('status') && is_numeric($request->status)) { $conditions['markers.status'] = $request->status; }

        return DataTables::eloquent($this->mainService->getMainListQuery($conditions))->make();
    }

    /**
     * マーカー管理一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // ステータスリスト追加
        return parent::index()->with([
            'type_list' => Common::getMarkerTypeList(),
            'status_list' => Common::getOpenStatusList(),
        ]);
    }

    /**
     * モーダルに必要なデータを取得
     * @param $marker_id
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
     * マーカーの所有ユーザ情報取得
     * @param $id
     * @throws \Exception
     */
    public function marker_users($id, UserService $userService) {
        
        // ユーザに紐づいているマーカーを取得
        return DataTables::eloquent($userService->getMarkerUserQuery($id))->make();
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
        // 保存処理モード
        $register_mode = $request->register_mode;
        
        // 除外項目
        $input = $request->except($this->except());

        if(is_null($request->image_flg)) {
            // 強制削除フラグがONの場合、専用画像名をDBに保存
            if(empty($request->file('upload_image')) && $request->delete_flg_on === 'true') {
                $input['image_file'] = config('const.out_image');
            }
            
            // 強制削除フラグがOFFでかつ画像がアップロードされていない場合、nullをDBに保存
            if(empty($request->file('upload_image')) && $request->delete_flg_on === 'false') {
                $input['image_file'] = null;
            }
        }

        // 画像あり
        if ($request->hasFile('upload_image')) {
            // 編集の場合、登録済みの画像削除
            if ($register_mode == "edit") {
                Common::removeImage($request->image_file);
            }
            // 画像の新規保存
            $input["image_file"] = Common::saveImage($request->file('upload_image'));
        }

        return $input;
    }

    /**
     * 削除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function remove(Request $request) {
        $this->mainService->remove($request->id);
        $this->userMarkerService->cascade($request->id);

        return redirect(route($this->mainRoot))->with('info_message', $this->mainTitle.'情報を削除しました');
    }

    /**
     * バリデーション設定
     * @param Request $request
     * @return array
     */
    public function validation_rules(Request $request)
    {
        // バリデーションチェック
        return [
            'price'         => ['integer'],
            'upload_image'  => ['required', 'image', 'max:1024'],
        ];
    }

    /**
     * バリデーションメッセージ
     * @param Request $request
     * @return array
     */
    public function validation_message(Request $request) {
        return [
            'price.integer'   => '価格は半角数字で入力してください',

            'upload_image.required' => '画像の設定は必須項目です',
            'upload_image.image' => '画像は"jpeg, png, bmp, gif, or svg"形式のみでアップロードしてください',
            'upload_image.max' => '画像は1,024kb以下しか登録できません',
        ];
    }
}
