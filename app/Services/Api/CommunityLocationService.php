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
    public function getCommunityLocationQuery($conditions=[], $order=[]) {
        $query = $this->model()->query();
        
        $query->leftJoin('markers', 'community_locations.marker_id', 'markers.id')
              ->leftJoin('users', 'community_locations.user_id', 'users.id')
              ->select( 'community_locations.id as location_id', 'community_locations.name as location_name', 
                        'community_locations.image_file', 'community_locations.created_at', 'community_locations.memo', 
                        'community_locations.latitude', 'community_locations.longitude',
                        'markers.id as marker_id', 'markers.name as marker_name', 'markers.type as marker_type',
                        'users.id as user_id', 'users.name as user_name'
                )
              ->where('community_locations.del_flg', '=', 0);

        // 検索条件がある場合は検索を実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }

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

        return $query;
    }
    
    /**
     * ロケーション登録の本人確認
     * 引数1: ロケーションID, 引数2: ユーザID
     */
    public function isRegisterUser($location_id, $user_id) {
        // 対象ロケーションのユーザIDとログインユーザのIDが一致するか確認
        return $this->searchExists(['id' => $location_id, 'user_id' => $user_id]);
    }
}