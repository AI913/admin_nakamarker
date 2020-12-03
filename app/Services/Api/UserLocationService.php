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
     * 引数1: ユーザID, 引数2: ロケーションID(一覧を表示する場合は要省略)
     */
    public function getUserLocationQuery($user_id, $location_id=null) {
        $query = $this->model()->query();
        
        $query->leftJoin('markers', 'user_locations.marker_id', 'markers.id')
              ->select( 'user_locations.id as location_id', 'user_locations.name as location_name', 
                        'user_locations.image_file', 'user_locations.created_at', 'user_locations.memo', 
                        'user_locations.latitude', 'user_locations.longitude',
                        'markers.name as marker_name', 'markers.type as marker_type'
                )
              ->where('user_locations.user_id', '=', $user_id)
              ->where('user_locations.del_flg', '=', 0);

        // ロケーション情報の詳細を取得する際に設定
        if(!is_null($location_id)) {
            $query->where('user_locations.id', '=', $location_id);
        }
        return $query;
    }
}