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
     * マーカーの一覧データを取得
     * 引数：ソート条件 
     */
    public function getMarkerQuery($order=[]) {
        $query = $this->model()->query();

        $query->select('id as marker_id', 'type', 'name', 'description', 'image_file',
                       'price', 'charge_flg', 'status')
              ->where('del_flg', '=', 0);

        // ソート条件
        foreach($order as $key => $value) {
            switch ($value) {
                // 作成日時の昇順
                case 99:
                    $query->orderBy('created_at', 'asc');
                break;
                // 作成日時の降順
                case -99:
                    $query->orderBy('created_at', 'desc');
                break;
            }
        }

        return $query;
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
                // 作成日時の昇順
                case 99:
                    $query->orderBy('user_markers.created_at', 'asc');
                break;
                // 作成日時の降順
                case -99:
                    $query->orderBy('user_markers.created_at', 'desc');
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

    /**
     * コミュニティが所有するデータリスト
     * 引数1：コミュニティID, 引数2：ソート条件 
     */
    public function getCommunityMarkerQuery($community_id, $order=[]) {
        $query = $this->model()->query();

        $query->leftJoin('community_markers', 'markers.id', '=', 'community_markers.marker_id')
              ->select('markers.type', 'markers.name', 'markers.image_file', 'markers.description',
                       'community_markers.id as history_id', 'community_markers.marker_id',
                       'community_markers.updated_at as community_markers_updated_at')
              ->where('community_markers.community_id', '=', $community_id)
              ->where('community_markers.del_flg', '=', 0);

        // ソート条件
        foreach($order as $key => $value) {
            switch ($value) {
                // 作成日時の昇順
                case 99:
                    $query->orderBy('community_markers.created_at', 'asc');
                break;
                // 作成日時の降順
                case -99:
                    $query->orderBy('community_markers.created_at', 'desc');
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