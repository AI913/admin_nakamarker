<?php
namespace App\Services\Model;

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
     * マーカーのDL数を取得
     * 
     */
    private function getDownloadCountQuery() {
        $query = $this->model()->query();

        $query->leftJoin('user_markers', 'markers.id', '=', 'user_markers.marker_id')
              ->selectRaw('count(user_markers.user_id) as total_counts')
              ->addSelect('user_markers.marker_id')
              ->groupByRaw('user_markers.marker_id')
              ->where('markers.charge_flg', '!=', 3);

        return $query;
    }

    /**
     * マーカー一覧画面に表示するデータリスト
     * 引数：データの検索条件
     */
    public function getMainListQuery($conditions=null) {

        $query = $this->model()->query();

        $download = $this->getDownloadCountQuery();

        $query->leftJoinSub($download, 'd', 'markers.id', '=', 'd.marker_id')
              ->select('markers.*', 'd.total_counts');
        
        // DL数カラムのソートが降順の場合
        if (request()->order[0]['column'] && request()->order[0]['column'] == 6 && request()->order[0]['dir'] == 'desc') {
            $query->orderBy('d.total_counts', 'desc')
                    ->orderByRaw('d.total_counts IS NULL ASC')
                    ->orderBy('d.total_counts')
                    ->orderBy('markers.charge_flg');
        // DL数カラムのソートが昇順の場合
        }else if(request()->order[0]['column'] && request()->order[0]['column'] == 6 && request()->order[0]['dir'] == 'asc') {
            $query->orderBy('d.total_counts')
                  ->orderBy('markers.charge_flg', 'desc');
        }
        
        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }

        return $query;
    }

    /**
     * ユーザ管理画面のモーダルに表示するデータリスト
     * 引数：ユーザID
     */
    public function getUserMarkerQuery($user_id) {
        $query = $this->model()->query();

        $query->leftJoin('user_markers', 'markers.id', '=', 'user_markers.marker_id')
              ->select('markers.image_file', 'markers.name', 'markers.charge_flg', 'user_markers.id as user_markers_id', 
                       'user_markers.updated_at as user_markers_updated_at', 'user_markers.pay_charge_point', 'user_markers.pay_free_point')
              ->where('user_markers.user_id', '=', $user_id)
              ->where('user_markers.del_flg', '=', 0);

        return $query;
    }

}