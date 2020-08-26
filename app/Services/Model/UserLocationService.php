<?php
namespace App\Services\Model;

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
     */
    public function isUserLocationData($user_id) {
        $query = $this->model()->query();
        
        $query->where('user_locations.user_id', $user_id);
        $query->leftJoin('markers', 'user_locations.marker_id', 'markers.id')
              ->select( 'user_locations.id as location_id', 'user_locations.name as location_name', 'user_locations.image_file',
                        'user_locations.created_at', 'user_locations.memo',
                        'markers.name as marker_name',
                );
        return $query;
    }
}