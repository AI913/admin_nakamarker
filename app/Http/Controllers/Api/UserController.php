<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\UserService;
use App\Services\Api\UserPointsHistoryService;
use App\Services\Api\UserLocationService;
use App\Services\Api\MarkerService;
use App\Services\Api\CommunityService;
use App\Services\Api\ConfigService;
use Carbon\Carbon;

class UserController extends BaseApiController
{
    protected $mainService;
    protected $userPointHistoryService;
    protected $userLocationService;
    protected $markerService;
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
        MarkerService $markerService,
        CommunityService $communityService
    ) {
        $this->mainService  = $mainService;
        $this->userPointHistoryService = $userPointHistoryService;
        $this->userLocationService = $userLocationService;
        $this->configService = $configService;
        $this->markerService = $markerService;
        $this->communityService = $communityService;
    }

    /**
     * ユーザ一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function index() {
    //     
    // }

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
     * Firebaseログイン処理
     */
    public function register(Request $request) {

        try {
            // データを配列化
            $data = $request->all();

            // データを保存
            $user = $this->mainService->save($data);
    
            // ステータスOK
            return $this->success(['uid' => $user->firebase_uid]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * ワンタイムパスワード発行処理
     * ※期限は1週間単位で設定する
     */
    public function password(Request $request) {

        try {
            // パスワードをリターン
            $password = $this->mainService->issueOnetimePassword();
            
            // 発行したパスワードデータを保存(有効期限は共通設定テーブルから値を抽出)
            $data = [
                'id'               => Auth::user()->id, // ユーザID
                'onetime_password' => $password,
                'limit_date'       => Carbon::now()->addWeek($this->configService->searchOne(['key' => 'password_limit_date'])->value),
            ];
            // ユーザデータを更新
            $user = $this->mainService->save($data);

            // アプリ表示用にカスタマイズ
            $confirmPassword = str_split($password, 4);
            $confirmPassword = $confirmPassword[0].'-'.$confirmPassword[1].'-'.$confirmPassword[2];
    
            // ステータスOK
            return $this->success([
                'password' => $confirmPassword,
            ]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }

    /**
     * アプリ引き継ぎ時の認証処理
     */
    public function login(Request $request) {
        try {
            // データを配列化
            $conditions = $request->all();

            // データを保存
            if($this->mainService->searchExists($conditions)) {
                // ユーザ情報を取得
                $user = $this->mainService->searchOne($conditions);
                // ステータスOK
                return $this->success(['user_token' => $user->user_token]);
            }
            return $this->error(-9, ["message" => __FUNCTION__.":ユーザ名もしくはパスワードが違います"]);
        } catch (\Exception $e) {
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
            // 保存するデータを配列に格納
            $data = [
                'id'   => Auth::user()->id,
                'name' => $request->input('name'),
                'device_token' => $request->input('device_token')
            ];
            // ユーザ名の更新
            $user = $this->mainService->save($data);

            // ステータスOK
            return $this->success(['name' => $user->name]);

        } catch (\Exception $e) {
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
            $free_limit_date = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => Auth::user()->id, 'charge_flg' => 1, 'used_flg' => 0])->first();
            $limit_date = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => Auth::user()->id, 'charge_flg' => 2, 'used_flg' => 0])->first();

            // ステータスOK
            return $this->success([
                'free_points' => $free_points,
                'points' => $points,
                'free_limit_date' => $free_limit_date,
                'limit_date' => $limit_date
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
                $free_limit_date = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $data['from_user_id'], 'charge_flg' => 1, 'used_flg' => 0])->first();
                $limit_date = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => $data['from_user_id'], 'charge_flg' => 2, 'used_flg' => 0])->first();
                
                return $this->success([
                    'total_give_free_point' => $free_points,
                    'total_give_charge_point' => $points,
                    'free_limit_date' => $free_limit_date,
                    'limit_date' => $limit_date
                ]);

            } else if ($request->pay_point) {
                // ポイント消費処理
                $this->userPointHistoryService->getPayPointQuery(Auth::user()->id, $request->pay_point, $request->charge_flg);

                // ポイント取得
                $free_points = $this->mainService->getFreePointQuery(['user_points_histories.to_user_id' => $request->to_user_id])->get();
                $points = $this->mainService->getPointQuery(['user_points_histories.to_user_id' => $request->to_user_id])->get();

                // 有効期限の最も近いポイントをそれぞれ取得
                $free_limit_date = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => Auth::user()->id, 'charge_flg' => 1, 'used_flg' => 0])->first();
                $limit_date = $this->userPointHistoryService->getLimitDateBaseQuery(['to_user_id' => Auth::user()->id, 'charge_flg' => 2, 'used_flg' => 0])->first();

                return $this->success([
                    'total_give_free_point' => $free_points,
                    'total_give_charge_point' => $points,
                    'free_limit_date' => $free_limit_date,
                    'limit_date' => $limit_date
                ]);
            }
    
        } catch (\Exception $e) {
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
            // ユーザ情報の取得
            $user = $this->mainService->searchOne(['user_token' => $request->bearerToken()]);
            // ユーザの登録場所とそれに紐づくマーカー情報を取得
            $user_location = $this->userLocationService->getUserLocationQuery($user->id)->get();

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
            // マーカー情報の取得
            $marker = $this->markerService->searchOne(['type' => $request->input('marker_type'), 'name' => $request->input('marker_name')]);

            // 保存データを配列に格納
            $data = $request->all();
            $data['user_id'] = Auth::user()->id;
            $data['marker_id'] = Auth::user()->id;

            // 保存処理
            $location = $this->userLocationService->save($data);
            
            // ステータスOK
            return $this->success();
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
            // ユーザの登録場所とそれに紐づくマーカー情報を取得
            $this->userLocationService->remove($request->input('location_id'));

            // ステータスOK
            return $this->success();
        } catch (\Exception $e) {
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
            // ソート条件
            $order = [];
            if(key_exists('order', $request->all())) {
                $sort = $request->input('order'); 
                $order[$sort] = $sort;
            }
            // ユーザのマーカー情報を取得
            $user_marker = $this->markerService->getUserMarkerQuery(Auth::user()->id, $order)->get();

            // ステータスOK
            return $this->success([
                'marker_list' => $user_marker,
            ]);
        } catch (\Exception $e) {
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
            // ソート条件
            $order = [];
            if(key_exists('order', $request->all())) {
                $sort = $request->input('order'); 
                $order[$sort] = $sort;
            }
            // ユーザのコミュニティ情報を取得
            $user_community = $this->communityService->getUserCommunityQuery(Auth::user()->id, $order)->get();

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
     * ユーザー情報取得
     * @return \Illuminate\Http\JsonResponse
     */
    // public function user(Request $request) {
    //     try {
    //         // デバイストークン更新
    //         $this->loginService->updateDeviceToken($request->user()->email, $request->device_token);

    //         return $this->success(['user' => $this->userService->find($request->user()->id)]);
    //     } catch (\Exception $e) {
    //         return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
    //     }
    // }

}
