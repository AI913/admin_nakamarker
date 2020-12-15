<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\Community;

class CommunityService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(Community $model) {
        $this->model = $model;
    }

    
    /**
     * ログインユーザが加盟、もしくは加盟申請しているコミュニティデータを取得
     * 引数:ユーザID,  引数2：ソート条件 
     */
    public function getUserCommunityQuery($user_id, $order=[]) {
        $query = $this->model()->query();
    
        $query->leftJoin('community_histories', 'communities.id', 'community_histories.community_id')
              ->select('communities.id as community_id', 'communities.type', 'communities.name', 
                       'communities.description', 'communities.image_file',
                       'communities.status as community_status', 'community_histories.status as entry_status',
                )
              ->where('community_histories.user_id', '=', $user_id)
              ->where('community_histories.del_flg', '=', 0);
        
        // ソート条件
        foreach($order as $key => $value) {
            switch ($value) {
                // 作成日時の昇順
                case 99:
                    $query->orderBy('community_histories.created_at', 'asc');
                break;
                // 作成日時の降順
                case -99:
                    $query->orderBy('community_histories.created_at', 'desc');
                break;
                
            }
        }

        return $query;
    }

    /**
     * コミュニティの参加人数を取得
     * 
     */
    public function joinCount() {
        $query = $this->model()->query();

        $query->leftJoin('community_histories', 'communities.id', '=', 'community_histories.community_id')
              ->selectRaw('count(community_histories.user_id) as total_counts')
              ->addSelect('community_histories.community_id')
              ->groupByRaw('community_histories.community_id');

        return $query;
    }

    /**
     * コミュニティ一覧画面に表示するデータリスト
     * 引数：データの検索条件
     */
    public function getMainListQuery($conditions=null) {
        $query = $this->model()->query();

        $joinCount = $this->joinCount();

        $query->leftJoinSub($joinCount, 'j', 'communities.id', '=', 'j.community_id')
              ->select('communities.*', 'j.total_counts')
              ->where('communities.del_flg', '=', 0);
        
        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }

        return $query;
    }
}