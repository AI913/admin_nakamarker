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
     * 引数1：検索条件 引数2：ソート条件 
     */
    public function getMarkerQuery($conditions=[], $order=[]) {
        // 削除フラグ排除のため、searchQuery()を実行
        $query = $this->searchQuery($conditions)
                      ->select('id as marker_id', 'type', 'name', 'description', 
                               'image_file', 'price', 'charge_flg', 'status'
                        );

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
                // マーカー名の昇順
                case 1:
                    $query->orderBy('name', 'asc');
                break;
                // マーカー名の降順
                case -1:
                    $query->orderBy('name', 'desc');
                break;
                // マーカー種別の昇順
                case 2:
                    $query->orderBy('type', 'asc');
                break;
                // マーカー種別の降順
                case -2:
                    $query->orderBy('type', 'desc');
                break;
                // マーカー価格の昇順
                case 3:
                    $query->orderBy('price', 'asc');
                break;
                // マーカー価格の降順
                case -3:
                    $query->orderBy('price', 'desc');
                break;
                // マーカーの有料フラグで昇順
                case 4:
                    $query->orderBy('charge_flg', 'asc');
                break;
                // マーカーの有料フラグで降順
                case -4:
                    $query->orderBy('charge_flg', 'desc');
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