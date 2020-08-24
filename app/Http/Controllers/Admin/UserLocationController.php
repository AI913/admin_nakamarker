<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Lib\Common;
use App\Services\Model\UserLocationService;
use App\Services\Model\MarkerService;
use App\Services\Model\UserService;
use App\Services\Model\ConfigService;

class UserLocationController extends BaseAdminController
{
    protected $mainService;
    protected $userService;
    protected $markerService;

    /**
     * ユーザロケーション管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(UserLocationService $mainService, UserService $userService, MarkerService $markerService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/user-location";
        $this->mainTitle    = 'ユーザロケーション管理';

        // UserServiceとMarkerServiceをインスタンス化
        $this->userService = $userService;
        $this->markerService = $markerService;
    }

    /**
     * 登録項目除外配列(下記以外に不要な項目があればオーバーライド)
     * @return array
     */
    public function except() {
        return ["_token", "register_mode", "upload_image", "img_delete", "delete_flg_on", 'image_flg'];
    }

    /**
     * ユーザロケーションリスト取得
     * @param request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function main_list(Request $request) {
        // 〇検索条件
        $conditions = [];
        if ($request->id) { $conditions['user_locations.id'] = $request->id; }
        if ($request->name) { $conditions['user_locations.name@like'] = $request->name; }
        if ($request->user_name) { $conditions['users.name@like'] = $request->user_name; }
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['user' => [], 'marker' => []];

        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }

    /**
     * ロケーション編集機能
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {
        // 全マーカーデータと全コミュニティデータ取得
        $user_list = $this->userService->all();
        $marker_list = $this->markerService->all();

        // 編集対象のロケーションデータを取得
        $data = $this->mainService->find($id);

        // ロケーションデータに紐づいたユーザとマーカーを取得
        $user = $this->userService->searchOne(['id' => $data->user_id]);
        $marker = $this->markerService->searchOne(['id' => $data->marker_id]);

        // ユーザ名とマーカー名を$dataに追加
        $data['user_name'] = $user->name;
        $data['marker_name'] = $marker->name;

        return view($this->mainRoot.'/register', [
            'register_mode' => 'edit',
            'user_list' => $user_list,
            'marker_list' => $marker_list,
            'data' => $data
        ]);
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
            
            Common::removeImage($request->image_file);
        }
        return $input;
    }
}
