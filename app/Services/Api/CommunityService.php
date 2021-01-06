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
     * 全コミュニティデータを取得
     * 引数1：検索条件, 引数2: ソート条件
     */
    public function getCommunityQuery($conditions=[], $order=[]) {
        // communitiesテーブルからデータを取得
        $query = $this->searchQuery($conditions)->select('type', 'name', 'description', 'image_file');
        
        // ソート条件
        foreach($order as $key => $value) {
            switch ($value) {
                // 作成日時の昇順
                case 99:
                    $query->orderBy('communities.created_at', 'asc');
                break;
                // 作成日時の降順
                case -99:
                    $query->orderBy('communities.created_at', 'desc');
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

    /**
     * コミュニティのホスト権限の有無を確認
     * 引数1：コミュニティID, 引数2：ユーザID
     */
    public function isHostUser($community_id, $user_id) {

        // 対象コミュニティのホストIDとログインユーザのIDが一致するか確認
        return $this->searchExists(['id' => $community_id, 'host_user_id' => $user_id]);

    }
}