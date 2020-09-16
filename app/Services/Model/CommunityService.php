<?php
namespace App\Services\Model;

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
     * ユーザ一覧ページに表示するコミュニティデータを取得
     * 引数:ユーザID
     */
    public function getUserCommunityQuery($user_id) {
        $query = $this->model()->query();
    
        $query->leftJoin('community_histories', 'communities.id', 'community_histories.community_id')
              ->leftJoin('users', 'community_histories.user_id', 'users.id')
              ->select('communities.*', 'users.id as user_id', 'community_histories.status as entry_status', 
                        'community_histories.memo', 'community_histories.id as community_history_id'
                )
              ->where('community_histories.user_id', '=', $user_id)
              ->where('community_histories.del_flg', '=', 0);
              
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
              ->orderBy('communities.id')
              ->where('communities.del_flg', '=', 0);
        
        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }

        return $query;
    }
}