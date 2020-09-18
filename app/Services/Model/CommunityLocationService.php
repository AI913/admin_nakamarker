<?php
namespace App\Services\Model;

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
     * コミュニティロケーション一覧ページに表示するデータを取得
     * 引数1: コミュニティID, 引数2: 検索条件
     */
    public function getCommunityLocationQuery($community_id, $conditions=null) {
        $query = $this->model()->query();

        $query->leftJoin('markers', 'community_locations.marker_id', 'markers.id')
              ->leftJoin('users', 'community_locations.user_id', 'users.id')
              ->select( 'community_locations.id as location_id', 'community_locations.user_id as user_id', 'community_locations.community_id as community_id',
                        'community_locations.name as location_name', 'community_locations.image_file', 
                        'community_locations.created_at', 'community_locations.memo', 
                        'community_locations.latitude', 'community_locations.longitude', 
                        'markers.name as marker_name', 'users.name as user_name'
                )
              ->where('community_locations.community_id', '=', $community_id)
              ->where('community_locations.del_flg', '=', 0);

        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }
        return $query;
    }

    /**
     * コミュニティロケーションデータの"備考"を取得
     * 引数1: ロケーションID
     */
    public function getLocationMemoQuery($location_id) {
        $query = $this->model()->query();

        $query->select('memo')
              ->where('id', '=', $location_id);

        return $query;
    }

    /**
     * 指定IDデータ削除(論理削除)
     * @param $community_id
     * @return mixed
     * @throws \Exception
     */
    // public function remove($id) {
    //     \DB::beginTransaction();
    //     try {
    //         $model = $this->find($id);
    //         if ($common) {
    //             // 共通DBレイアウトの場合
    //             $model->del_flg = 1;
    //             $model->updated_at = Carbon::now();
    //             $model->update_user_id = self::getUserId();
    //         }
    //         $model->save();
    //         if ($transaction) \DB::commit();
    //         return $model;
    //     } catch (\Exception $e) {
    //         if ($transaction) \DB::rollBack();
    //         \Log::error('database remove error:'.$e->getMessage());
    //         throw new \Exception($e);
    //     }
    // }

    /**
     * コミュニティ削除時のロケーション削除処理
     * 
     */
    public function cascade($community_id) {
        \DB::beginTransaction();
        try {
            $model = $this->model()::query();
            $model = $model->where('community_id', '=', $community_id)->get();
            
            foreach($model as $value){
                $value->del_flg = 1;
                $value->save();
            }

            \DB::commit();
            return;
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('database remove error:'.$e->getMessage());
            throw new \Exception($e);
        }
        
    }
}