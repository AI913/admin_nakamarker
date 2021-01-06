<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\UserService;
use App\Services\Api\UserPointsHistoryService;
use App\Services\Api\UserLocationService;
use App\Services\Api\UserMarkerService;
use App\Services\Api\MarkerService;
use App\Services\Api\CommunityHistoryService;
use App\Services\Api\CommunityService;
use App\Services\Api\ConfigService;

class UserController extends BaseApiController
{
    protected $mainService;
    protected $userPointHistoryService;
    protected $userLocationService;
    protected $userMarkerService;
    protected $markerService;
    protected $communityHistoryService;
    protected $communityService;
    protected $configService;
    

    /**
     * ユーザ管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(
        UserService $mainService, 
        ConfigService $configService, 
        UserPointsHistoryService $userPointHistoryService,
        UserLocationService $userLocationService,
        UserMarkerService $userMarkerService,
        MarkerService $markerService,
        CommunityService $communityService,
        CommunityHistoryService $communityHistoryService
    ) {
        $this->mainService  = $mainService;
        $this->userPointHistoryService = $userPointHistoryService;
        $this->userLocationService = $userLocationService;
        $this->userMarkerService = $userMarkerService;
        $this->configService = $configService;
        $this->markerService = $markerService;
        $this->communityHistoryService = $communityHistoryService;
        $this->communityService = $communityService;
    }

    /**
     * ユーザー情報取得(ユーザートークンをキーとする)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request) {
        try {
            // ステータスOK
            return $this->success(['name' => Auth::user()->name]);

        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * ユーザ作成
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request) {
        try {
            \DB::beginTransaction();

            // ユーザ名の登録
            $user = $this->mainService->save($request->all(), true, false);

            // ユーザトークンの発行
            $token = $this->mainService->issueUserToken($user->id);

            // ユーザ情報更新
            $data = [
                'id'            => $user->id,
                'user_token'    => $token
            ];
            $user = $this->mainService->save($data, false, false);

            \DB::commit();

            // ステータスOK
            return $this->success(['user_token' => $user->user_token]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * ユーザ名の編集
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request) {
        try {
            \DB::beginTransaction();
            // 保存するデータを配列に格納
            $data = [
                'id'   => Auth::user()->id,
                'name' => $request->input('name'),
                'device_token' => $request->input('device_token')
            ];
            // ユーザ名の更新
            $user = $this->mainService->save($data);

            \DB::commit();
            // ステータスOK
            return $this->success(['name' => $user->name]);

        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * ユーザーの所有ポイント情報取得(ユーザーIDをキーとする)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pointInfo(Request $request) {
        try {
            // ポイント取得
            $free_points = $this->mainService->getFreePointQuery(['user_points_histories.to_user_id' => Auth::user()->id])->get();
            $points = $this->mainService->getPointQuery(['user_points_histories.to_user_id' => Auth::user()->id])->get();

            // 有効期限の最も近いポイントをそれぞれ取得
            $remaining_free_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $user->id, 'charge_flg' => 1, 'used_flg' => 0])->first();
            $remaining_charge_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $user->id, 'charge_flg' => 2, 'used_flg' => 0])->first();

            // ステータスOK
            return $this->success([
                'total_give_free_point' => $free_points,
                'total_give_charge_point' => $points,
                'remaining_free_point' => $remaining_free_point,
                'remaining_charge_point' => $remaining_charge_point
            ]);

        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * ユーザーポイントの更新処理(ポイント付与を実施した場合)
     * @param Request $request
     * @param UserPointService $userPointService
     * @return array
     * @throws \Exception
     */
    public function pointUpdate(Request $request) {
        try {
            \DB::beginTransaction();
            // ポイントが増加する場合
            if($request->give_point) {
                // 選択したユーザの総ポイント数を取得
                $points = $this->mainService->getUserPointQuery(['users.id' => Auth::user()->id])->first();

                // ポイント付与の種別がギフトかつ無料の付与ポイントが無料の所持ポイントより多い場合
                if($request->type == 2 && $request->charge_flg == 1 && $request->give_point > $points->free_total_points) {
                    return ['status' => -2];
                }
                // ポイント付与の種別がギフトかつ有料の付与ポイントが有料の所持ポイントより多い場合
                if($request->type == 2 && $request->charge_flg == 2 && $request->give_point > $points->total_points) {
                    return ['status' => -2];
                }

                // 保存データを配列に格納
                $data = [
                    'type'              => 2,
                    'give_point'        => $request->give_point,
                    'charge_flg'        => $request->charge_flg,
                    'to_user_id'        => $request->to_user_id,
                    'from_user_id'      => Auth::user()->id,
                    'status'            => 2,
                    'update_user_id'    => Auth::user()->id,
                ];

                // ポイント履歴の更新or作成
                $model = $this->userPointHistoryService->save($data);
                // ポイント付与の種別がギフトだった場合
                if($model->type == 2) {
                    // ポイントをギフトしたユーザのポイントを消費
                    $this->userPointHistoryService->getPayPointQuery($data['from_user_id'], $data['give_point'], $data['charge_flg']);
                }

                // ポイント取得
                $free_points = $this->mainService->getFreePointQuery(['user_points_histories.to_user_id' => $data['from_user_id']])->get();
                $points = $this->mainService->getPointQuery(['user_points_histories.to_user_id' => $data['from_user_id']])->get();

                // 有効期限の最も近いポイントをそれぞれ取得
                $remaining_free_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $data['from_user_id'], 'charge_flg' => 1, 'used_flg' => 0])->first();
                $remaining_charge_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $data['from_user_id'], 'charge_flg' => 2, 'used_flg' => 0])->first();
                
                \DB::commit();
                return $this->success([
                    'total_give_free_point' => $free_points,
                    'total_give_charge_point' => $points,
                    'remaining_free_point' => $remaining_free_point,
                    'remaining_charge_point' => $remaining_charge_point
                ]);

            } else if ($request->pay_point) {
                // ポイント消費処理
                $this->userPointHistoryService->getPayPointQuery(Auth::user()->id, $request->pay_point, $request->charge_flg);

                // ポイント取得
                $free_points = $this->mainService->getFreePointQuery(['user_points_histories.to_user_id' => $request->to_user_id])->get();
                $points = $this->mainService->getPointQuery(['user_points_histories.to_user_id' => $request->to_user_id])->get();

                // 有効期限の最も近いポイントをそれぞれ取得
                $remaining_free_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $user->id, 'charge_flg' => 1, 'used_flg' => 0])->first();
                $remaining_charge_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $user->id, 'charge_flg' => 2, 'used_flg' => 0])->first();

                \DB::commit();
                return $this->success([
                    'total_give_free_point' => $free_points,
                    'total_give_charge_point' => $points,
                    'remaining_free_point' => $remaining_free_point,
                    'remaining_charge_point' => $remaining_charge_point
                ]);
            }
    
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * 登録場所情報の取得
     * @param $id
     * @throws \Exception
     */
    public function locationInfo(Request $request) {
        try {
            // 検索条件
            $conditions = [];
            $conditions['user_id'] = \Auth::user()->id;
            // ロケーション情報の詳細を取得する際に設定
            if ($request->input('location_id')) { $conditions['id'] = $request->input('location_id'); }
            // ソート条件
            $order = $this->setSort($request);
            // ユーザの登録場所とそれに紐づくマーカー情報を取得
            $user_location = $this->userLocationService->getUserLocationQuery($conditions, $order);

            // ステータスOK
            return $this->success([
                'location_list' => $user_location,
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * 場所情報の登録
     * @param $id
     * @throws \Exception
     */
    public function locationRegister(Request $request) {
        try {
            \DB::beginTransaction();
            // マーカー情報の取得
            $marker = $this->markerService->searchOne(['type' => $request->input('marker_type'), 'name' => $request->input('marker_name')]);

            // 保存データを配列に格納
            $data = $request->all();
            $data['user_id'] = Auth::user()->id;
            $data['marker_id'] = $marker->id;

            // 保存処理
            $location = $this->userLocationService->save($data);
            
            \DB::commit();
            // ステータスOK
            return $this->success();
        } catch (\Exception $e) {
            \DB::rollback();
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
            // ユーザの登録場所とそれに紐づくマーカー情報を取得
            $this->userLocationService->remove($request->input('location_id'));

            // ステータスOK
            \DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * マーカー情報の取得
     * @param $id
     * @throws \Exception
     */
    public function markerInfo(Request $request) {
        try {
            // 検索条件
            $conditions = [];
            $conditions['id'] = Auth::user()->id;
            // ソート条件
            $order = $this->setSort($request);
            // ユーザのマーカー情報を取得
            $user_marker = $this->mainService->getUserMarkerQuery($conditions, $order);
            // $user_marker = $this->markerService->getUserMarkerQuery(Auth::user()->id, $order)->get();

            // ステータスOK
            return $this->success([
                'marker_list' => $user_marker,
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * マーカー情報の更新
     * @param $id
     * @throws \Exception
     */
    public function markerUpdate(Request $request) {
        try {
            \DB::beginTransaction();
            // マーカー情報の取得
            $marker = $this->markerService->searchOne(['id' => $request->input('marker_id')]);
            
            // ポイントの消費
            $points = $this->userPointHistoryService->getPayPointQuery(Auth::user()->id, $marker->price, $marker->charge_flg);

            // 保存データを配列に格納
            $data = [
                'user_id' => Auth::user()->id,
                'marker_id' => $request->input('marker_id'),
                'pay_free_point' => $points['free_points'],
                'pay_charge_point' => $points['charge_points'],
            ];

            // 保存処理
            $user_marker = $this->userMarkerService->save($data);

            // ポイント取得
            $free_points = $this->mainService->getFreePointQuery(['user_points_histories.to_user_id' => Auth::user()->id])->get();
            $points = $this->mainService->getPointQuery(['user_points_histories.to_user_id' => Auth::user()->id])->get();

            if (!$points) {
                // URL無効エラー
                return $this->error(-2, ['message' => Message::ERROR_REGISTER_TOKEN]);
            }

            // 有効期限の最も近いポイントをそれぞれ取得
            $remaining_free_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => Auth::user()->id, 'charge_flg' => 1, 'used_flg' => 0])->first();
            $remaining_charge_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => Auth::user()->id, 'charge_flg' => 2, 'used_flg' => 0])->first();

            \DB::commit();
            // ステータスOK
            return $this->success([
                'total_give_free_point' => $free_points,
                'total_give_charge_point' => $points,
                'remaining_free_point' => $remaining_free_point,
                'remaining_charge_point' => $remaining_charge_point
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
    
    /**
     * コミュニティ情報の取得
     * @param $id
     * @throws \Exception
     */
    public function communityInfo(Request $request) {
        try {
            // 検索条件
            $conditions = [];
            $conditions['id'] = Auth::user()->id;
            // ソート条件
            $order = $this->setSort($request);
            // ユーザのコミュニティ情報を取得
            // $user_community = $this->communityService->getUserCommunityQuery(Auth::user()->id, $order)->get();
            $user_community = $this->mainService->getUserCommunityQuery($conditions, $order);

            // ステータスOK
            return $this->success([
                'community_list' => $user_community,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * コミュニティ参加情報を更新
     */
    public function communityUpdate(Request $request) {
        try {
            \DB::beginTransaction();

            // 保存データを配列に格納
            $data['user_id'] = Auth::user()->id;
            $data['community_id'] = $request->input('community_id');
            $data['status'] = config('const.community_history_apply'); // 申請中の値をセット
            // 保存処理
            $this->communityHistoryService->save($data);

            \DB::commit();
            // ステータスOK
            return $this->success();
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

}
