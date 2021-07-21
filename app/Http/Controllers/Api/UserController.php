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
use App\Lib\Message;
use Carbon\Carbon;

class UserController extends BaseApiController
{
    protected $userService;
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
        UserService $userService,
        ConfigService $configService,
        UserPointsHistoryService $userPointHistoryService,
        UserLocationService $userLocationService,
        UserMarkerService $userMarkerService,
        MarkerService $markerService,
        CommunityService $communityService,
        CommunityHistoryService $communityHistoryService
    ) {
        $this->userService  = $userService;
        $this->userPointHistoryService = $userPointHistoryService;
        $this->userLocationService = $userLocationService;
        $this->userMarkerService = $userMarkerService;
        $this->configService = $configService;
        $this->markerService = $markerService;
        $this->communityHistoryService = $communityHistoryService;
        $this->communityService = $communityService;

        // フォルダ名の設定
        $this->folder = 'users';
    }

    /**
     * ユーザー情報取得(ユーザートークンをキーとする)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request)
    {
        try {
            $userData = Auth::user();
            return $this->success([
                'name' => $userData->name,
                'image_url' => $userData->image_url
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * ユーザ作成
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        try {
            \DB::beginTransaction();

            $data = [
                'name' => $request->input('name'),
                'device_token' => $request->input('device_token')
            ];

            if ($request->hasFile('image')) {
                $data['image_file'] = $this->fileSave($request);
            }

            $user = $this->userService->create($data);

            \DB::commit();

            return $this->success(['user_token' => $user->user_token]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * ユーザ名の編集
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            // 保存するデータを配列に格納

            \Log::debug($request);

            $data = [
                'id'   => Auth::user()->id,
                'name' => $request->input('name'),
                'device_token' => $request->input('device_token')
            ];
            // 画像ありの場合は保存処理を実行
            if ($request->hasFile('image')) {
                $data['image_file'] = $this->fileSave($request);
            }

            $this->userService->save($data);

            \DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * ユーザーの所有ポイント情報取得(ユーザーIDをキーとする)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pointInfo(Request $request)
    {
        try {
            $free_points = $this->userService->getFreePointQuery(['user_points_histories.to_user_id' => Auth::user()->id, 'charge_type' => 1,])->first();
            $chargePoints = $this->userService->getPointQuery(['user_points_histories.to_user_id' => Auth::user()->id, 'charge_type' => 2,])->first();

            $remaining_free_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => Auth::user()->id, 'charge_type' => 1, 'used_flg' => 0])->first();
            $remaining_charge_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => Auth::user()->id, 'charge_type' => 2, 'used_flg' => 0])->first();

            $limitDateData = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => Auth::user()->id])->first();

            // 以下、データが無い場合のnullチェック。
            $totalFree = 0;
            if (isset($free_points)) {
                $totalFree = (int)$free_points['free_total_points'];
            }

            $totalCharge = 0;
            if (isset($chargePoints)) {
                $totalCharge = (int)$chargePoints['total_points'];
            }

            $remainingFree = 0;
            if (isset($remaining_free_point)) {
                $remainingFree = (int)$remaining_free_point['remaining_points'];
            }

            $remainingCharge = 0;
            if (isset($remaining_charge_point)) {
                $remainingCharge = (int)$remaining_charge_point['remaining_points'];
            }

            $limitDate = "";
            if (isset($limitDateData)) {
                $limitDate = $limitDateData['limit_date'];
            }

            return $this->success([
                'total_give_free_point' => $totalFree,
                'total_give_charge_point' => $totalCharge,
                'limit_date' => $limitDate,
                'remaining_free_point' => $remainingFree,
                'remaining_charge_point' => $remainingCharge
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * ユーザーポイントの更新処理(ポイント付与を実施した場合)
     * @param Request $request
     * @param UserPointService $userPointService
     * @return array
     * @throws \Exception
     */
    public function pointUpdate(Request $request)
    {
        try {
            \DB::beginTransaction();
            // ポイントが増加する場合
            if ($request->give_point) {
                // ログインユーザの総ポイント数を取得
                $points = $this->userService->getUserPointQuery(['users.id' => Auth::user()->id])->first();

                // 付与対象のユーザが存在するか確認
                if (
                    $this->userService->searchExists(['id' => $request->to_user_id, 'status' => config('const.user_app_unsubscribe')]) ||
                    $this->userService->searchExists(['id' => $request->to_user_id, 'status' => config('const.user_app_account_stop')])
                ) {
                    return $this->error(-3, ["message" => Message::ERROR_NOT_EXISTS_USER]);
                }

                // ポイント付与の種別がギフトかつ無料の付与ポイントが無料の所持ポイントより多い場合
                if (
                    $request->type == config('const.point_gift') &&
                    $request->charge_type == config('const.charge_type_off') &&
                    $request->give_point > $points->free_total_points
                ) {
                    return $this->error(-2, ["message" => Message::ERROR_NOT_OVER_FREE_POINT]);
                }
                // ポイント付与の種別がギフトかつ有料の付与ポイントが有料の所持ポイントより多い場合
                if (
                    $request->type == config('const.point_gift') &&
                    $request->charge_type == config('const.charge_type_on') &&
                    $request->give_point > $points->total_points
                ) {
                    return $this->error(-2, ["message" => Message::ERROR_NOT_OVER_CHARGE_POINT]);
                }

                // 保存データを配列に格納
                $data = [
                    'type'              => 2,
                    'give_point'        => $request->give_point,
                    'charge_type'       => $request->charge_type,
                    'to_user_id'        => $request->to_user_id,
                    'from_user_id'      => Auth::user()->id,
                    'status'            => 2,
                    'update_user_id'    => Auth::user()->id,
                ];

                // ポイント履歴の更新or作成
                $model = $this->userPointHistoryService->save($data);
                // ポイント付与の種別がギフトだった場合
                if ($model->type == 2) {
                    // ポイントをギフトしたユーザのポイントを消費
                    $this->userPointHistoryService->getPayPointQuery($data['from_user_id'], $data['give_point'], $data['charge_type']);
                }

                // ポイント取得
                $free_points = $this->userService->getFreePointQuery(['user_points_histories.to_user_id' => $data['from_user_id']])->get();
                $points = $this->userService->getPointQuery(['user_points_histories.to_user_id' => $data['from_user_id']])->get();

                // 有効期限の最も近いポイントをそれぞれ取得
                $remaining_free_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $data['from_user_id'], 'charge_type' => config('const.charge_type_off'), 'used_flg' => 0])->first();
                $remaining_charge_point = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $data['from_user_id'], 'charge_type' => config('const.charge_type_on'), 'used_flg' => 0])->first();

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
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * 登録場所情報の取得
     * @param $id
     * @throws \Exception
     */
    public function locationInfo(Request $request)
    {
        try {
            // 検索条件
            $conditions = [];
            $conditions['user_id'] = \Auth::user()->id;
            // ロケーション情報の詳細を取得する際に設定
            if ($request->input('location_id')) {
                $conditions['id'] = $request->input('location_id');
            }
            $order = $this->getSortOrder($request);

            $returnData = [];

            foreach ($this->userLocationService->getUserLocationQuery($conditions, $order) as $locations) {
                array_push($returnData, [
                    'location_id' => $locations['location_id'],
                    'name' => $locations['location_name'],
                    'latitude' => $locations['latitude'],
                    'longitude' => $locations['longitude'],
                    'image_file' => $locations['image_url'],
                    'marker_type' => $locations['marker']['marker_type'],
                    'marker_name' => $locations['marker']['marker_name'],
                    'memo' => $locations['memo']
                ]);
            }

            return $this->success(['location_data_list' => $returnData]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * 場所情報の登録
     * @param $id
     * @throws \Exception
     */
    public function locationRegister(Request $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->all();
            // アプリ側からlocation_idで飛んでくるのでidに変換
            $data['id'] = $data['location_id'];
            $data['user_id'] = Auth::user()->id;
            // 画像ありの場合は保存処理を実行
            if ($request->hasFile('image')) {
                $data['image_file'] = $this->fileSave($request, config('const.user_locations'));
            }

            $locationData = $this->userLocationService->save($data);
            \DB::commit();
            return $this->success(['location_data' => $locationData]);
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * 登録場所情報の削除
     * @param $id
     * @throws \Exception
     */
    public function locationRemove(Request $request)
    {
        try {
            \DB::beginTransaction();
            // ユーザの登録場所とそれに紐づくマーカー情報を取得
            $this->userLocationService->remove($request->input('location_id'));

            // ステータスOK
            \DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * マーカー情報の取得
     * @param $id
     * @throws \Exception
     */
    public function getUserMarker(Request $request)
    {
        try {
            \Log::debug($request);

            // 検索条件
            $conditions = [];
            $conditions['id'] = Auth::user()->id;

            $order = $this->getSortOrder($request);

            $returnData = [];

            foreach ($this->userService->getUserMarkerQuery($conditions, $order) as $markers) {
                array_push($returnData, [
                    'marker_id' => $markers['marker_id'],
                    'type' => $markers['type'],
                    'name' => $markers['name'],
                    'search_word' => $markers['search_word'],
                    'description' => $markers['description'],
                    'price' => $markers['price'],
                    'charge_type' => $markers['charge_type'],
                    'status' => $markers['status'],
                    'image_file' => $markers['image_url']
                ]);
            }

            return $this->success(['marker_list' => $returnData]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * マーカー情報の更新
     * @param $request['is_free'] = ポイント足りなくてもマーカーを更新できるかどうか(string型で飛んできてます)
     * @throws \Exception
     */
    public function markerUpdate(Request $request)
    {
        try {
            \DB::beginTransaction();
            $isFree = $request['is_free'] == "true";
            // マーカー情報の取得
            $marker = $this->markerService->searchOne(['id' => $request->input('marker_id')]);

            // ポイントの消費
            $points = $this->userPointHistoryService->getPayPointQuery(Auth::user()->id, $marker->price, $marker->charge_type);

            if ($isFree) {
                $data = [
                    'user_id' => Auth::user()->id,
                    'marker_id' => $request->input('marker_id'),
                    'pay_free_point' => 0,
                    'pay_charge_point' => 0,
                ];
            } else {
                $data = [
                    'user_id' => Auth::user()->id,
                    'marker_id' => $request->input('marker_id'),
                    'pay_free_point' => $points['free_points'],
                    'pay_charge_point' => $points['charge_points'],
                ];
            }
            
            $this->userMarkerService->save($data);

            // ポイント取得
            $free_points = $this->userService->getFreePointQuery(['user_points_histories.to_user_id' => Auth::user()->id])->first();
            $chargePoints = $this->userService->getPointQuery(['user_points_histories.to_user_id' => Auth::user()->id])->first();

            if (!$chargePoints && !$isFree) {
                return $this->error(-2, ['message' => Message::ERROR_REGISTER_TOKEN]);
            }

            \DB::commit();

            // 以下、データが無い場合のnullチェック。
            $totalFree = 0;
            if (isset($free_points)) {
                $totalFree = (int)$free_points['free_total_points'];
            }

            $totalCharge = 0;
            if (isset($chargePoints)) {
                $totalCharge = (int)$chargePoints['total_points'];
            }

            return $this->success([
                'total_give_free_point' => $totalFree,
                'total_give_charge_point' => $totalCharge
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * コミュニティ情報の取得
     * @param $id
     * @throws \Exception
     */
    public function getUserJoinedCommunity(Request $request)
    {
        try {
            // 検索条件
            $conditions = [];
            $conditions['id'] = Auth::user()->id;
            
            $order = $this->getSortOrder($request);

            return $this->success(['community_list' => $this->userService->getUserCommunityQuery($conditions, $order)]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * コミュニティ参加情報を更新
     */
    public function communityUpdate(Request $request)
    {
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
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }

    /**
     * マーカーIDから登録場所情報の取得
     * @param $request->marker_id マーカーID
     * @throws \Exception
     */
    public function getUserLocationFromMarkerId(Request $request)
    {
        try {
            $conditions = [
                'user_id'   => Auth::user()->id,
                'marker_id' => $request['marker_id']
            ];

            $returnData = [];
            foreach ($this->userLocationService->getUserLocationQuery($conditions) as $data) {
                array_push($returnData, [
                    'location_id'   => $data['location_id'],
                    'name' => $data['location_name'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'image_file' => $data['image_file']
                ]);
            }

            return $this->success(['userlocation_list' => $returnData]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }
}
