<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\CommunityService;
use App\Services\Api\CommunityMarkerService;
use App\Services\Api\CommunityHistoryService;
use App\Services\Api\MarkerService;

class CommunityController extends BaseApiController
{
    protected $mainService;
    protected $communityMarkerService;
    protected $communityHistoryService;
    protected $markerService;

    /**
     * コミュニティ管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(
        CommunityService $mainService,
        CommunityMarkerService $communityMarkerService,
        CommunityHistoryService $communityHistoryService,
        MarkerService $markerService
    ) {
        $this->mainService  = $mainService;
        $this->communityMarkerService = $communityMarkerService;
        $this->communityHistoryService = $communityHistoryService;
        $this->markerService = $markerService;
        // フォルダ名の設定
        $this->folder = 'communities';
    }

    /**
     * コミュニティ一覧情報取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        try {
            // 検索条件
            $conditions = [];
            $conditions['status'] = config('const.open'); // "公開"で設定されているものだけに絞る
            if ($request->input('name')) { $conditions['communities.name@like'] = $request->input('name'); }
            if ($request->input('type') && is_numeric($request->input('type'))) { $conditions['communities.type'] = $request->input('type'); }
            // ソート条件
            $order = [];
            if(key_exists('order', $request->all())) {
                $sort = $request->input('order'); 
                $order[$sort] = $sort;
            }

            // コミュニティ一覧データを取得
            $communities = $this->mainService->getCommunityQuery($conditions, $order)->get();

            // ステータスOK
            return $this->success(['communities' => $communities]);

        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * コミュニティ情報の登録
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        try {
            \DB::beginTransaction();

            // データを配列化
            $data = $request->all();

            // コミュニティの種別を設定
            $data['status'] ? $data['type'] = config('const.community_personal_open') : $data['type'] = config('const.community_personal');

            // コミュニティ一覧データを取得
            $this->mainService->save($data);

            \DB::commit();
            // ステータスOK
            return $this->success();

        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * コミュニティ情報の更新
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request) {
        try {
            \DB::beginTransaction();

            // 修正対象のコミュニティデータを取得
            $community = $this->mainService->searchOne(['id' => $request->input('community_id')]);

            // ログインユーザにホスト権限があるかどうか確認
            if($community->host_id !== \Auth::user()->id) {
                // 無ければエラーを飛ばす
                throw new \Exception("Not Authorized");
            }

            // データを配列化
            $data = $request->all();
            // コミュニティの種別を設定
            $data['status'] ? $data['type'] = config('const.community_personal_open') : $data['type'] = config('const.community_personal');
            // コミュニティIDを保存用のキーに変換
            $data['community_id'] ? $data['id'] = $data['community_id'] : '';

            // 画像ありの場合は保存処理を実行
            if($request->hasFile('image')) {
                $data['image_file'] = $this->fileSave($request);
            }

            // コミュニティデータを保存
            $this->mainService->save($data);

            \DB::commit();
            // ステータスOK
            return $this->success();

        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * コミュニティマーカーのリストを取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markerInfo(Request $request) {
        try {
            // コミュニティに加盟しているかどうか確認
            if(!$this->communityHistoryService->isCommunityUser($request->input('community_id'))) {
                return $this->error(-10, ["message" => 'コミュニティに加盟していないため、権限がありません']);
            }

            // ソート条件
            $order = [];
            if(key_exists('order', $request->all())) {
                $sort = $request->input('order'); 
                $order[$sort] = $sort;
            }
            // コミュニティのマーカー情報を取得
            $community_marker = $this->markerService->getCommunityMarkerQuery($request->input('community_id'), $order)->get();

            // ステータスOK
            return $this->success([
                'marker_list' => $community_marker,
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * コミュニティマーカーの登録
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markerRegister(Request $request) {
        try {
            \DB::beginTransaction();

            // データを配列化
            $data = $request->all();

            // コミュニティマーカーの保存
            $this->communityMarkerService->save($data);

            \DB::commit();
            // ステータスOK
            return $this->success();

        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
    
}
