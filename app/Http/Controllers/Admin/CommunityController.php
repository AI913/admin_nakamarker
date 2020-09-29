<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\CommunityService;
use App\Services\Model\CommunityLocationService;
use App\Services\Model\CommunityHistoryService;
use App\Services\Model\UserService;
use App\Services\Model\MarkerService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Validation\Rule;

class CommunityController extends BaseAdminController
{
    protected $mainService;
    protected $communityLocationService;
    protected $communityHistoryService;
    protected $markerService;

    public function __construct(
        CommunityService $mainService, 
        CommunityLocationService $communityLocationService, 
        CommunityHistoryService $communityHistoryService,
        UserService $userService,
        MarkerService $markerService
    )
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/community";
        $this->mainTitle    = 'コミュニティ管理';

        $this->communityLocationService = $communityLocationService;
        $this->communityHistoryService = $communityHistoryService;

        // MarkerServiceとUserServiceをインスタンス化
        $this->userService = $userService;
        $this->markerService = $markerService;

        // テーブル名の設定
        $this->table = 'communities';
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
        if ($request->has('type') && is_numeric($request->type)) { $conditions['communities.type'] = $request->type; }
        // statusのリクエストがあり、かつリクエストが数値の場合に検索条件の値をセットする
        if ($request->has('status') && is_numeric($request->status)) { $conditions['communities.status'] = $request->status; }
        
        return DataTables::eloquent($this->mainService->getMainListQuery($conditions))->make();
    }

    public function index()
    {
        // ステータスリスト追加
        return parent::index()->with([
            'type_list'   => Common::getCommunityTypeList(),
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
    public function community_locations(Request $request, $id) {
        // マーカ名をマーカーのIDに変換
        if ($request->marker_name) { 
            $marker = $this->markerService->searchOne(['name@like' => $request->marker_name]);
        }

        // 〇検索条件
        $conditions = [];
        if ($request->id) { $conditions['community_locations.id'] = $request->id; }
        if ($request->marker_name) { $conditions['community_locations.marker_id'] = $marker->id; }
        if ($request->name) { $conditions['community_locations.name@like'] = $request->name; }

        // コミュニティの登録場所とそれに紐づくマーカー情報を取得
        return DataTables::eloquent($this->communityLocationService->getCommunityLocationQuery($id, $conditions))->make();
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
    // public function saveBefore(Request $request) {
    //     // 保存処理モード
    //     $register_mode = $request->register_mode;
        
    //     // 除外項目
    //     $input = $request->except($this->except());

    //     if(is_null($request->image_flg)) {
    //         // 強制削除フラグがONの場合、専用画像名をDBに保存
    //         if(empty($request->file('upload_image')) && $request->delete_flg_on === 'true') {
    //             $input['image_file'] = config('const.out_image');
    //         }
            
    //         // 強制削除フラグがOFFでかつ画像がアップロードされていない場合、nullをDBに保存
    //         if(empty($request->file('upload_image')) && $request->delete_flg_on === 'false') {
    //             $input['image_file'] = null;
    //         }
    //     }

    //     // 画像あり
    //     if ($request->hasFile('upload_image')) {
    //         // 編集の場合、登録済みの画像削除
    //         if ($register_mode == "edit") {
    //             Common::removeImage($request->image_file);
    //         }
    //         // 画像の新規保存
    //         $input["image_file"] = Common::saveImage($request->file('upload_image'));
    //     }

    //     return $input;
    // }

    /**
     * 削除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function remove(Request $request) {
        $this->mainService->remove($request->id);
        $this->communityHistoryService->cascade($request->id);
        $this->communityLocationService->cascade($request->id);

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
            'name'     => [Rule::unique('communities')->ignore($request['id'], 'id')->where('del_flg', '=', 0)],
            
            'upload_image'  => ['image', 'max:1024'], // upload_imageの記載は必須
        ];
    }

    /**
     * バリデーションメッセージ
     * @param Request $request
     * @return array
     */
    public function validation_message(Request $request) {
        return [
            'name.unique'    => 'このコミュニティ名はすでに使用されています',

            'upload_image.image' => '画像は"jpeg, png, bmp, gif, or svg"形式のみでアップロードしてください',
            'upload_image.max' => '画像は1,024kb以下しか登録できません',
        ];
    }
}
