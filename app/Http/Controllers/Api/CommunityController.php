<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\CommunityService;
use App\Services\Api\CommunityMarkerService;
use App\Services\Api\CommunityHistoryService;
use App\Services\Api\CommunityLocationService;
use App\Services\Api\MarkerService;
use App\Services\Api\UserService;
use App\Lib\Message;

class CommunityController extends BaseApiController
{
    protected $mainService;
    protected $communityMarkerService;
    protected $communityHistoryService;
    protected $communityLocationService;
    protected $markerService;
    protected $userService;

    /**
     * コミュニティ管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(
        CommunityService $mainService,
        CommunityMarkerService $communityMarkerService,
        CommunityHistoryService $communityHistoryService,
        CommunityLocationService $communityLocationService,
        MarkerService $markerService,
        UserService $userService
    ) {
        $this->mainService  = $mainService;
        $this->communityMarkerService = $communityMarkerService;
        $this->communityHistoryService = $communityHistoryService;
        $this->communityLocationService = $communityLocationService;
        $this->markerService = $markerService;
        $this->userService = $userService;
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
            $order = $this->setSort($request);

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
            $userId = \Auth::user()->id;
            $data = $request->all();
            // コミュニティの種別を設定
            $data['status'] ? $data['type'] = config('const.community_personal_open') : $data['type'] = config('const.community_personal');
            // ホストユーザの設定
            $data['host_user_id'] = $userId;
            // 画像ありの場合は保存処理を実行
            if($request->hasFile('image')) {
                $data['image_file'] = $this->fileSave($request);
            }

            $community = $this->mainService->save($data);

            // 作成したコミュニティに作成者自身を参加
            $addData = [
                'community_id' => $community->id,
                'user_id' => $userId,
                'status' => 1
            ];
            $this->communityHistoryService->save($addData);

            \DB::commit();

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

            // コミュニティのホストかどうかを確認
            if(!$this->mainService->isHostUser($request->input('community_id'), \Auth::user()->id)) {
                return $this->error(-10, ["message" => Message::ERROR_NOT_HOST]);
            }
            
            // データを配列化
            $data = $request->all();
            // コミュニティの種別を設定
            key_exists('status', $data) && $data['status'] ? $data['type'] = config('const.community_personal_open') : $data['type'] = config('const.community_personal');
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
            if(!$this->communityHistoryService->isCommunityUser($request->input('community_id'), \Auth::user()->id)) {
                return $this->error(-10, ["message" => Message::ERROR_NOT_COMMUNIRY_MEMBER]);
            }

            // 検索条件
            $conditions = [];
            $conditions['id'] = $request->input('community_id');
            // ソート条件
            $order = $this->setSort($request);
            // コミュニティのマーカー情報を取得
            $community_marker = $this->mainService->getCommunityMarkerQuery($conditions, $order);

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

            // コミュニティのホストかどうかを確認
            if(!$this->mainService->isHostUser($request->input('community_id'), \Auth::user()->id)) {
                return $this->error(-10, ["message" => Message::ERROR_NOT_HOST]);
            }
            // マーカーの重複チェック
            if($this->communityMarkerService->isDuplicateMarker($request->input('community_id'), $request->input('marker_id'))) {
                return $this->error(-2, ["message" => Message::ERROR_NOT_MARKER_DUPLICATE]);
            }

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

    /**
     * コミュニティマーカーの更新
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markerUpdate(Request $request) {
        try {
            \DB::beginTransaction();
            
            // コミュニティのホストかどうかを確認
            if(!$this->mainService->isHostUser($request->input('community_id'), \Auth::user()->id)) {
                return $this->error(-10, ["message" => Message::ERROR_NOT_HOST]);
            }
            // マーカーの重複チェック
            if($this->communityMarkerService->isDuplicateMarker($request->input('community_id'), $request->input('marker_id'))) {
                return $this->error(-2, ["message" => Message::ERROR_NOT_MARKER_DUPLICATE]);
            }

            // データを配列化
            $data = $request->all();
            // 履歴IDを保存用のキーに変換
            $data['history_id'] ? $data['id'] = $data['history_id'] : '';

        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * コミュニティへの加入を希望するユーザ一覧を取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo(Request $request) {
        try {
            // コミュニティのホストかどうかを確認
            if(!$this->mainService->isHostUser($request->input('community_id'), \Auth::user()->id)) {
                return $this->error(-10, ["message" => Message::ERROR_NOT_HOST]);
            }
            
            // 検索条件
            $conditions = [];
            if ($request->input('community_id')) { $conditions['id'] = $request->input('community_id'); }
            // ソート条件
            $order = $this->setSort($request);

            // コミュニティ一覧データを取得
            $communities = $this->mainService->getApplyListQuery(config('const.community_history_apply'), $conditions, $order);

            // ステータスOK
            return $this->success(['communities' => $communities]);

        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
    /**
     * コミュニティへの加入申請情報を更新
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userUpdate(Request $request) {
        try {
            \DB::beginTransaction();
            // データを配列にセット
            $data = [];
            if($request->input('history_id')) { $data['id'] = $request->input('history_id'); }
            if($request->input('status')) { $data['status'] = $request->input('status'); }

            // コミュニティデータを保存
            $this->communityHistoryService->save($data);

            \DB::commit();
            // ステータスOK
            return $this->success();

        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * コミュニティロケーションのリストを取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function locationInfo(Request $request) {
        try {
            // コミュニティに加盟しているかどうか確認
            if(!$this->communityHistoryService->isCommunityUser($request->input('community_id'), \Auth::user()->id)) {
                return $this->error(-10, ["message" => Message::ERROR_NOT_COMMUNIRY_MEMBER]);
            }

            // 検索条件
            $conditions = [];
            $conditions['community_locations.community_id'] = $request->input('community_id');
            if($request->input('location_id')) { $conditions['community_locations.id'] = $request->input('location_id'); }
            if($request->input('user_id')) { $conditions['community_locations.user_id'] = $request->input('user_id'); }
            if($request->input('marker_id')) { $conditions['community_locations.marker_id'] = $request->input('marker_id'); }
            // ソート条件
            $order = $this->setSort($request);
            // コミュニティのロケーション情報を取得
            $community_location = $this->communityLocationService->getCommunityLocationQuery($conditions, $order);

            // ステータスOK
            return $this->success([
                'location_list' => $community_location,
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * 登録場所情報の削除
     * @param $id
     * @throws \Exception
     */
    public function locationRemove(Request $request) {
        try {
            \DB::beginTransaction();
            
            // ロケーションを登録した本人では無い場合
            if(!$this->communityLocationService->isRegisterUser($request->input('location_id'), \Auth::user()->id)) {
                // 削除対象のロケーションに紐づくコミュニティIDを取得
                $community_id = $this->mainService->searchOne(['id' => $request->input('location_id')])->id;
                // コミュニティのホストかどうかを確認
                if(!$this->mainService->isHostUser($community_id, \Auth::user()->id)) {
                    return $this->error(-10, ["message" => Message::ERROR_NOT_HOST]);
                }
            }

            // コミュニティの登録場所を削除
            $this->communityLocationService->remove($request->input('location_id'));

            // ステータスOK
            \DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
    /**
     * コミュニティロケーションの登録
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function locationRegister(Request $request) {
        try {
            \DB::beginTransaction();

            // コミュニティに加盟しているかどうか確認
            if(!$this->communityHistoryService->isCommunityUser($request->input('community_id'), \Auth::user()->id)) {
                return $this->error(-10, ["message" => Message::ERROR_NOT_COMMUNIRY_MEMBER]);
            }

            $data = $request->all();
            // ロケーションIDを保存用のキーに変換
            $data['id'] = $data['location_id'];
            $data['user_id'] = \Auth::user()->id;
            if($request->hasFile('image')) {
                $data['image_file'] = $this->fileSave($request, config('const.community_locations'));
            }

            $comData = $this->communityLocationService->save($data);
            \DB::commit();
            return $this->success(['location_list' => $comData]);

        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * 場所登録の更新情報取得
     * @param $request->offset 何件目から取得するか
     * @throws \Exception
     */
    public function locationNews(Request $request) {
        try {
          return $this->success(['updated_list' => $this->communityLocationService->getCommunityLocationUpadateQuery([],['updated_at' => 'desc'], $request->input('offset'))]);
        } catch (\Exception $e) {
          return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
}
