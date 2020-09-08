<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\CommunityService;
use App\Services\Model\CommunityLocationService;
use App\Services\Model\UserService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

class CommunityController extends BaseAdminController
{
    protected $mainService;
    protected $communityLocationService;

    public function __construct(CommunityService $mainService, CommunityLocationService $communityLocationService, UserService $userService)
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/community";
        $this->mainTitle    = 'コミュニティ管理';

        $this->communityLocationService = $communityLocationService;
        $this->userService = $userService;
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
        if ($request->id) { $conditions['communities.id'] = $request->id; }
        if ($request->name) { $conditions['communities.name@like'] = $request->name; }
        // statusのリクエストがあり、かつリクエストが数値の場合に検索条件の値をセットする
        if ($request->has('status') && is_numeric($request->status)) { $conditions['communities.status'] = $request->status; }
        
        return DataTables::eloquent($this->mainService->getMainListQuery($conditions))->make();
    }

    public function index()
    {
        // ステータスリスト追加
        return parent::index()->with([
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

    /**
     * ユーザ情報取得
     * @param $id
     * @throws \Exception
     */
    public function community_users($id) {
        
        // コミュニティに紐づくユーザ情報を取得
        return DataTables::eloquent($this->userService->getCommunityUserQuery($id))->make();
    }

    /**
     * ユーザ情報の詳細を取得
     * @param $id
     * @throws \Exception
     */
    public function community_users_detail($community_id, $user_id) {
        // コミュニティの登録場所とそれに紐づくマーカーの詳細情報を取得
        $data = $this->userService->getCommunityUserQuery($community_id, $user_id)->first();
        
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
    public function community_locations($id) {
        // コミュニティの登録場所とそれに紐づくマーカー情報を取得
        return DataTables::eloquent($this->communityLocationService->getCommunityLocationQuery($id))->make();
    }

    /**
     * 登録場所情報の詳細を取得
     * @param $id
     * @throws \Exception
     */
    public function community_locations_detail($community_id, $location_id) {
        // コミュニティの登録場所とそれに紐づくマーカーの詳細情報を取得
        $data = $this->communityLocationService->getCommunityLocationQuery($community_id, $location_id)->first();
        
        return [
            'status' => 1,
            'data' => $data,
        ]; 
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
}
