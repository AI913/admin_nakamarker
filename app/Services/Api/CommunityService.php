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
      return $this->searchQuery($conditions, $order)
                  ->select('id as community_id', 'type', 'name', 'description', 'image_file', 'host_user_id')
                  ->with('hostUser:id,name as host_user_name,image_file')
                  ->get();
    }

    /**
     * コミュニティが所有するデータリスト
     * 引数1：検索条件, 引数2：ソート条件
     */
    public function getCommunityMarkerQuery($conditions=[], $order=[]) {
        // 削除フラグ排除のため、searchQuery()を実行
        $query = $this->searchQuery($conditions, $order)
                      ->select('id')
                      ->with(['marker' => function ($query) {
                        // community_markersテーブルとmarkersテーブルの値をクエリすることが可能
                        $query->select('community_markers.id as history_id', 'community_markers.marker_id',
                                       'community_markers.updated_at', 'markers.type', 'markers.name',
                                       'markers.image_file', 'markers.description');
                      }])
                      ->get();
        // 結合したmarkersテーブルと中間テーブルの値に絞り込み
        $query = $query[0]->marker;
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
     * コミュニティの加入申請一覧に表示するデータリスト
     * 引数1：コミュニティの申請状況, 引数2：データの検索条件, 引数3：ソート条件
     */
    public function getApplyListQuery($status, $conditions=[], $order=[]) {
        // 削除フラグ排除のため、searchQuery()を実行
        $query = $this->searchQuery($conditions, $order)
                      ->select('id')
                      ->with(['user' => function ($query) use ($status) {
                          // community_historiesテーブルとusersテーブルの値をクエリすることが可能
                          $query->select('community_histories.id as history_id', 'users.name as user_name', 'community_histories.memo')
                                ->where('community_histories.status', '=', $status);
                      }])
                      ->get();

        // 結合したusersテーブルと中間テーブルの値に絞り込み
        $query = $query[0]->user;
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
