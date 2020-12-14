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
     * 引数1：ユーザID, 引数2：ソート条件 
     */
    public function getUserMarkerQuery($user_id, $order=[]) {
        $query = $this->model()->query();

        $query->leftJoin('user_markers', 'markers.id', '=', 'user_markers.marker_id')
              ->select('markers.type', 'markers.name', 'markers.image_file', 'markers.description',
                       'user_markers.marker_id', 'user_markers.updated_at as user_markers_updated_at')
              ->where('user_markers.user_id', '=', $user_id)
              ->where('user_markers.del_flg', '=', 0);

        // ソート条件
        foreach($order as $key => $value) {
            switch ($value) {
                // デフォルト設定
                case 99:
                    $query->orderBy('user_markers.created_at', 'desc');
                break;
                case -99:
                    $query->orderBy('user_markers.created_at', 'asc');
                break;
                // マーカーの種別で昇順
                case 1:
                    $query->orderBy('markers.type', 'asc');
                break;
                // マーカーの名前で昇順
                case 2:
                    $query->orderBy('markers.name', 'asc');
                break;
            }
        }

        return $query;
    }
}