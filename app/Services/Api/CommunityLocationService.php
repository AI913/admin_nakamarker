<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\CommunityLocation;

class CommunityLocationService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(CommunityLocation $model) {
        $this->model = $model;
    }

    /**
     * コミュニティマーカーで登録されたデータを取得
     * 引数1: コミュニティID, 引数2: ロケーションID(一覧を表示する場合は要省略), 引数3：ソート条件
     */
    public function getCommunityLocationQuery($community_id, $location_id=null, $order=[]) {
        $query = $this->model()->query();
        
        $query->leftJoin('markers', 'community_locations.marker_id', 'markers.id')
              ->leftJoin('users', 'community_locations.user_id', 'users.id')
              ->select( 'community_locations.id as location_id', 'community_locations.name as location_name', 
                        'community_locations.image_file', 'community_locations.created_at', 'community_locations.memo', 
                        'community_locations.latitude', 'community_locations.longitude',
                        'markers.name as marker_name', 'markers.type as marker_type',
                        'users.name as user_name'
                )
              ->where('community_locations.community_id', '=', $community_id)
              ->where('community_locations.del_flg', '=', 0);

        // ソート条件
        foreach($order as $key => $value) {
            switch ($value) {
                // 作成日時の昇順
                case 99:
                    $query->orderBy('community_locations.created_at', 'asc');
                break;
                // 作成日時の降順
                case -99:
                    $query->orderBy('community_locations.created_at', 'desc');
                break;
                
            }
        }

        // ロケーション情報の詳細を取得する際に設定
        if(!is_null($location_id)) {
            $query->where('community_locations.id', '=', $location_id);
        }
        return $query;
    }
}