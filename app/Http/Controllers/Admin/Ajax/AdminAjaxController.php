<?php

namespace App\Http\Controllers\Admin\Ajax;

use App\Http\Controllers\Controller;
use App\Model\Service;
use App\Model\UserPoint;
use App\Services\Api\ZenrinService;
use App\Services\Model\CardService;
use App\Services\Model\ConfigService;
use App\Services\Model\DivisionService;
use App\Services\Model\GeofenceService;
use App\Services\Model\InformationService;
use App\Services\Model\ServiceService;
use App\Services\Model\StoreService;
use App\Services\Model\StorePointService;
use App\Services\Model\UserLocationService;
use App\Services\Model\UserPointService;
use App\Services\Model\UserService;
use App\Services\Model\UserServiceConditionService;
use App\Services\Model\UserServiceService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * 管理画面用ajaxコントローラー
 * Class AdminApiController
 * @package App\Http\Controllers\Api
 */
class AdminAjaxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Emailの重複チェック(true:重複している)
     * @param Request $request
     * @param UserService $userService
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function isDuplicateEmail(Request $request, UserService $userService) {
        return ['is_email' => $userService->isUserForEmail($request->email, $request->id)];
    }

    /**
     * ポイント日時の重複チェック(0:重複していない)
     * @param Request $request
     * @param StorePointService $storePointService
     * @return number[]
     */
    public function isDuplicatePointDate(Request $request, StorePointService $storePointService) {

        return [
            'is_date' => $storePointService->isPointFormToDate($request->start_date, $request->end_date,  $request->store_id, $request->point_id)
        ];
    }

    /**
     * 今日の対象ポイントが通常ポイントかどうか
     * @param Request $request
     * @param StorePointService $storePointService
     * @return array
     */
    public function isDefaultPoint(Request $request, StorePointService $storePointService) {
        return [
            'status' => $storePointService->isDefaultPoint($request->store_id)
        ];
    }

    /**
     * 店舗名の重複チェック(true:重複している)
     * @param Request $request
     * @param StoreService $storeService
     * @return mixed[]
     */
    public function isDuplicateStoreName(Request $request, StoreService $storeService) {
        return ['is_name' => $storeService->isStoreForName($request->store_name, $request->store_id)];
    }

    /**
     * ジオフェンス名の重複チェック(true:重複している)
     * @param Request $request
     * @param GeofenceService $geofenceService
     * @return mixed[]
     */
    public function isDuplicateGeofenceName(Request $request, GeofenceService $geofenceService) {
        return ['is_name' => $geofenceService->isGeofenceForName($request->geofence_name, $request->geofence_id)];
    }

    /**
     * 共通設定のキーの重複チェック(true:重複している)
     * @param Request $request
     * @param ConfigService $configService
     * @return mixed[]
     */
    public function isDuplicateConfigKey(Request $request, ConfigService $configService) {
        return ['is_key' => $configService->isConfigForKey($request->config_key, $request->config_id)];
    }

    /**
     * 指定会社IDの事業部データ取得
     * @param Request $request
     * @param DivisionService $divisionService
     * @return array
     */
    public function getDivisionListForCompany(Request $request, DivisionService $divisionService) {
        $condition = ['company_id' => $request->company_id];
        // 事業部長以下は自身の事業部のみ
        if (!\Auth::user()->isCompanyOver()) {
            $condition['id'] = \Auth::user()->division_id;
        }
        return ['divisions' => $divisionService->searchList($condition)];
    }

    /**
     * 指定会社IDと事業部IDから店舗データ取得
     * @param Request $request
     * @param StoreService $storeService
     * @return array
     */
    public function getStoreListForCompanyAndDivision(Request $request, StoreService $storeService) {
        $condition = ['company_id' => $request->company_id, 'division_id' => $request->division_id];
        // 店舗責任者以下は自身の店舗のみ
        if (!\Auth::user()->isDivisionOver()) {
            $condition['id'] = \Auth::user()->store_id;
        }
        return ['stores' => $storeService->searchList()];
    }
    /**
     * 指定IDの店舗データ取得
     * @param Request $request
     * @param StoreService $storeService
     * @return array
     */
    public function getStore(Request $request, StoreService $storeService) {
        return ['store' => $storeService->find($request->id)];
    }

    /**
     * 指定IDのジオフェンスデータ取得
     * @param Request $request
     * @param GeofenceService $geofenceService
     * @return array
     */
    public function getGeofence(Request $request, GeofenceService $geofenceService) {
        return ['geofence' => $geofenceService->find($request->id)];
    }

    /**
     * 指定IDのサービス券データ取得
     * @param Request $request
     * @param InformationService $informationService
     * @return array
     */
    public function getService(Request $request, ServiceService $serviceService) {
        return ['service' => $serviceService->find($request->id)];
    }

    /**
     * 指定IDのお知らせデータ取得
     * @param Request $request
     * @param InformationService $informationService
     * @return array
     */
    public function getInformation(Request $request, InformationService $informationService) {
        return ['information' => $informationService->find($request->id)];
    }

    /**
     * サービス券配信条件登録処理
     * @param Request $request
     * @param ServiceService $serviceService
     * @return array
     * @throws \Exception
     */
    public function registerServiceCondition(Request $request, ServiceService $serviceService) {
        return $serviceService->registerCondition($request);
    }

    /**
     * お知らせ配信条件登録処理
     * @param Request $request
     * @param InformationService $informationService
     * @return int
     * @throws \Exception
     */
    public function registerInformationCondition(Request $request, InformationService $informationService) {
        return $informationService->registerCondition($request);
    }

    /**
     * 顧客管理からのユーザーポイント履歴作成処理
     * @param Request $request
     * @param UserPointService $userPointService
     * @return array
     * @throws \Exception
     */
    public function createPointHistory(Request $request, UserPointService $userPointService) {
        if (!$request->user_id) {
            return ['status' => -1];
        }
        // ポイント履歴作成
        $userPointService->save([
            'user_id'   => $request->user_id,
            'user_location_id'  => 0,
            'location_id'       => 0,
            'location_type'     => config('const.location_type_system'),
            'point_type'        => $request->point_type,
            'grant_point'       => $request->point,
            'receive_flg'       => 1,
            'get_date'          => date('Y-m-d H:i:s'),
            'limit_date'        => $request->limit_date,
        ]);
        return ['status' => 1, "user_id" => $request->user_id];
    }

    /**
     * お知らせピックアップ更新
     * @param Request $request
     * @param InformationService $informationService
     * @return int
     */
    public function updateInformationPickUp(Request $request, InformationService $informationService) {
        return $informationService->updatePickUp($request);
    }

    /**
     * 指定メールアドレスユーザーの位置情報履歴取得
     * @param Request $request
     * @param UserLocationService $userLocationService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getUserLocationList(Request $request, UserLocationService $userLocationService) {
        return DataTables::eloquent($userLocationService->getLocationForEmailQuery($request->email))->make();
    }

    /**
     * 緯度経度変換(世界測地系⇒日本測地系)
     * @param Request $request
     * @param ZenrinService $zenrinService
     * @return array
     */
    public function getChangeLatLong(Request $request, ZenrinService $zenrinService) {
        $location1 = $zenrinService->getChangeLocation($request->latitude_1, $request->longitude_1);
        $location2 = "";
        if ($request->latitude_2 && $request->longitude_2) {
            $location2 = $zenrinService->getChangeLocation($request->latitude_2, $request->longitude_2);
        }

        return ['status' => 1, "location_1" => $location1, "location_2" => $location2];
    }
    /**
     * 受け取りステータス更新
     * @param Request $request
     * @param UserPointService $userPointService
     * @return array
     */
    public function updateReceive(Request $request, UserPointService $userPointService) {
        $point = $userPointService->find($request->point_id);
        $point->receive_flg = !$point->receive_flg;
        $point->save();

        return ['status' => 1, 'receive' => $point->receive_flg];
    }
    /**
     * ユーザーサービス券配信履歴取得
     * @param Request $request
     * @param UserLocationService $userLocationService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getUserServiceConditionList(Request $request, UserServiceConditionService $userServiceConditionService) {
        return DataTables::eloquent($userServiceConditionService->getHistoryQuery($request))->make();
    }
    /**
     * ユーザーサービス券配信履歴取得
     * @param Request $request
     * @param UserLocationService $userLocationService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getUserServiceList(Request $request, UserServiceService $userServiceService) {
        return DataTables::eloquent($userServiceService->getHistoryQuery($request))->make();
    }

    /**
     * 対象サービス券の配信履歴リセット処理
     * @param Request $request
     * @param UserServiceConditionService $userServiceConditionService
     */
    public function resetUserServiceCondtion(Request $request, UserServiceConditionService $userServiceConditionService) {
        return $userServiceConditionService->updateResetHistory($request->service_id);
    }
}
