<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Lib\Common;
use App\Services\Model\CommunityLocationService;
use App\Services\Model\MarkerService;
use App\Services\Model\CommunityService;
use App\Services\Model\ConfigService;

class CommunityLocationController extends BaseAdminController
{
    protected $mainService;
    protected $markerService;
    protected $communityService;

    /**
     * コミュニティロケーション管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(CommunityLocationService $mainService, MarkerService $markerService, CommunityService $communityService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/community-location";
        $this->mainTitle    = 'コミュニティロケーション管理';

        // MarkerServiceとCommunityServiceをインスタンス化
        $this->markerService = $markerService;
        $this->communityService = $communityService;
    }

    /**
     * 登録項目除外配列(下記以外に不要な項目があればオーバーライド)
     * @return array
     */
    public function except() {
        return ["_token", "register_mode", "upload_image", "img_delete", "delete_flg_on", "marker_name", "marker_id", 'image_flg'];
    }

    /**
     * コミュニティロケーションリスト取得
     * @param request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function main_list(Request $request) {
        // 〇検索条件
        $conditions = [];
        if ($request->id) { $conditions['community_locations.id'] = $request->id; }
        if ($request->name) { $conditions['community_locations.name@like'] = $request->name; }
        if ($request->community_id) { $conditions['community_locations.community_id'] = $request->community_id; }
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = ['user' => [], 'marker' => [], 'community' => []];

        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }

    /**
     * ロケーション作成機能
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        // 全マーカーデータと全コミュニティデータ取得
        $marker_list = $this->markerService->all();
        $community_list = $this->communityService->all();

        // マーカーリスト&コミュニティリスト追加
        return parent::create()->with([
            'marker_list' => $marker_list,
            'community_list' => $community_list,
        ]);
    }

    /**
     * ロケーション編集機能
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {
        // 全マーカーデータと全コミュニティデータ取得
        $marker_list = $this->markerService->all();
        $community_list = $this->communityService->all();

        // 編集対象のロケーションデータを取得
        $data = $this->mainService->find($id);

        // ロケーションデータに紐づいたマーカーとコミュニティを取得
        $marker = $this->markerService->searchOne(['id' => $data->marker_id]);
        $community = $this->communityService->searchOne(['id' => $data->community_id]);

        // マーカー名とコミュニティ名を$dataに追加
        $data['marker_name'] = $marker->name;
        $data['community_name'] = $community->name;

        return view($this->mainRoot.'/register', [
            'register_mode' => 'edit',
            'marker_list' => $marker_list,
            'community_list' => $community_list,
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
        // 保存処理モード
        $register_mode = $request->register_mode;

        // 選択したマーカーとコミュニティのデータをそれぞれ取得
        $marker = $this->markerService->searchOne(['name' => $request->marker_name]);
        $community = $this->communityService->searchOne(['name' => $request->community_name]);

        // 除外項目
        $input = $request->except($this->except());

        // マーカーとコミュニティのIDを配列に追加
        $input['marker_id'] = $marker->id;
        $input['community_id'] = $community->id;

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
}
