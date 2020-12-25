<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\CommunityHistory;

class CommunityHistoryService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(CommunityHistory $model) {
        $this->model = $model;
    }

    /**
     * コミュニティの加入申請一覧に表示するデータリスト
     * 引数1：データの検索条件, 引数2：ソート条件
     */
    public function getApplyListQuery($conditions=[], $order=[]) {
        $query = $this->model()->query();

        // community_historiesテーブルからデータを取得
        $query = $query->leftJoin('users', 'community_histories.user_id', 'users.id')
                       ->from('community_histories')
                       ->select('community_histories.id as history_id', 'users.name as user_name', 'community_histories.memo')
                       ->where('community_histories.del_flg', '=', 0);

        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }
        
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
}