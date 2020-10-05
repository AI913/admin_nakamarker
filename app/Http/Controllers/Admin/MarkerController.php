<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\MarkerService;
use App\Services\Model\UserService;
use App\Services\Model\UserMarkerService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\Rule;
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
        // テーブル名の設定
        $this->table = 'markers';
    }

    /**
     * 登録項目除外配列(下記以外に不要な項目があればオーバーライド)
     * @return array
     */
    public function except() {
        return ["_token", "register_mode", "upload_image", "img_delete", "delete_flg_on", 'image_flg', 'file_path', 'file_src'];
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
        $conditions['markers.del_flg'] = 0;
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
        // 画像を設定した履歴がセッションに残っている場合
        // もしくは編集画面で画像の変更は無く、また画像の消去ボタンを押さずに保存した場合
        if (\Session::get('file_path') || $request->image_file && $request->img_delete == 0) {
            return [
                'name'          => [Rule::unique('markers')->ignore($request['id'], 'id')->where('del_flg', '=', 0)],
                'price'         => ['integer'],
                'upload_image'  => ['image', 'max:1024'], // upload_imageの記載は必須
            ];
        }
        
        // バリデーションチェック(画像が設定されていない場合)
        return [
            'name'          => [Rule::unique('markers')->ignore($request['id'], 'id')->where('del_flg', '=', 0)],
            'price'         => ['integer'],
            'upload_image'  => ['required', 'image', 'max:1024'], // upload_imageの記載は必須
        ];
    }

    /**
     * バリデーションメッセージ
     * @param Request $request
     * @return array
     */
    public function validation_message(Request $request) {
        return [
            'name.unique'    => 'このマーカ名はすでに使用されています',

            'price.integer'   => '価格は半角数字で入力してください',

            'upload_image.required' => '画像の設定は必須項目です',
            'upload_image.image' => '画像は"jpeg, png, bmp, gif, or svg"形式のみでアップロードしてください',
            'upload_image.max' => '画像は1,024kb以下しか登録できません',
        ];
    }
}
