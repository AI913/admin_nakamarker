<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\UserLocation;

class UserLocationService extends BaseService
{
/**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(UserLocation $model) {
        $this->model = $model;
    }

    /**
     * ユーザ一覧ページに表示する登録場所データを取得
     * 引数1: 検索条件, 引数2: ソート条件
     */
    public function getUserLocationQuery($conditions=[], $order=[]) {
        // 削除フラグ排除のため、searchQuery()を実行
        $query = $this->searchQuery($conditions)
                      ->select('id as location_id', 'name as location_name', 'latitude', 'longitude', 'image_file', 'memo', 'marker_id')
                      ->with('marker:id,name as marker_name,type as marker_type')
                      ->get();
        
        // ソート条件
        foreach($order as $key => $value) {
            switch ($key) {
                // 作成日時の昇順
                case 99:
                    $query = $query->sortBy('created_at');
                break;
                // 作成日時の降順
                case -99:
                    $query = $query->sortByDesc('created_at');
                break;
                // ロケーション名で昇順
                case 1:
                    $query = $query->sortBy('location_name');
                break;
                // ロケーション名で降順
                case -1:
                    $query = $query->sortByDesc('location_name');
                break;
                // マーカーの種別で昇順
                case 2:
                    $query = $query->sortBy('marker.marker_type');
                break;
                // マーカーの種別で降順
                case -2:
                    $query = $query->sortByDesc('marker.marker_type');
                break;
                // マーカーの名前で昇順
                case 3:
                    $query = $query->sortBy('marker.marker_name');
                break;
                // マーカーの名前で降順
                case -3:
                    $query = $query->sortByDesc('marker.marker_name');
                break;
            }
        }

        return $query;
    }
}