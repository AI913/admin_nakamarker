<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\Marker;

class MarkerService extends BaseService
{
    /**
     * コンストラクタ
     * MarkerService constructor.
     */
    public function __construct(Marker $model) {
        $this->model = $model;
    }

    /**
     * ログインユーザが所有するデータリスト
     * 引数：ユーザID
     */
    public function getUserMarkerQuery($user_id) {
        $query = $this->model()->query();

        $query->leftJoin('user_markers', 'markers.id', '=', 'user_markers.marker_id')
              ->select('markers.type', 'markers.name', 'markers.image_file', 'markers.description',
                       'user_markers.marker_id', 'user_markers.updated_at as user_markers_updated_at')
              ->where('user_markers.user_id', '=', $user_id)
              ->where('user_markers.del_flg', '=', 0);

        return $query;
    }
}