<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\User;

class UserService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * 指定メールアドレスユーザーが存在するかどうかチェックする
     * @param $email
     * @param int $user_id
     * @return mixed
     */
    public function isUserForEmail($email, $user_id=0) {
        $conditions["email"] = $email;
        // ユーザーIDが指定されていれば
        if ($user_id) {
            $conditions["users.id@not"] = $user_id;
        }

        return $this->searchExists($conditions);
    }

    /**
     * ポイント履歴から現在の無料ポイント数を算出
     */
    public function getFreePointQuery() {
        $query = $this->model()->query();

        $query->leftJoin('user_points_histories', 'users.id', 'user_points_histories.user_id')
              ->selectRaw('sum(user_points_histories.give_point) - sum(user_points_histories.pay_point) as free_total_points')
              ->addselect('user_points_histories.user_id')
              ->groupByRaw('user_points_histories.user_id')
              ->where('user_points_histories.charge_flg', '=', 1)
              ->where('user_points_histories.del_flg', '=', 0);

        return $query;
    }

    /**
     * ポイント履歴から現在の有料ポイント数を算出
     */
    public function getPointQuery() {
        $query = $this->model()->query();

        $query->leftJoin('user_points_histories', 'users.id', 'user_points_histories.user_id')
              ->selectRaw('sum(user_points_histories.give_point) - sum(user_points_histories.pay_point) as total_points')
              ->addselect('user_points_histories.user_id')
              ->groupByRaw('user_points_histories.user_id')
              ->where('user_points_histories.charge_flg', '=', 2)
              ->where('user_points_histories.del_flg', '=', 0);

        return $query;
    }

    /**
     * ユーザの所持ポイントを算出したデータの取得
     * 引数：データの検索条件
     */
    public function getUserPointQuery($conditions=null) {
        $query = $this->model()->query();

        // 無料ポイントと有料ポイントの算出クエリをそれぞれインスタンス化
        $free_points_query = $this->getFreePointQuery();
        $points_query = $this->getPointQuery();

        // サブクエリでポイントテーブルとユーザテーブルを結合
        $query->leftJoinSub($free_points_query, 'free_points', 'users.id', '=', 'free_points.user_id')
              ->leftJoinSub($points_query, 'charge_points', 'users.id', '=', 'charge_points.user_id')
              ->select('users.*', 'free_points.free_total_points', 'charge_points.total_points');

        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }
        return $query;
    }

    /**
     * マーカーの詳細モーダルに表示するユーザデータを取得
     * 引数:マーカーID
     */
    public function getMarkerUserQuery($marker_id) {
        $query = $this->model()->query();

        $query->leftJoin('user_markers', 'users.id', '=', 'user_markers.user_id')
              ->leftJoin('markers', 'user_markers.marker_id', '=', 'markers.id')
              ->select('user_markers.id as user_markers_id', 'user_markers.updated_at as user_markers_updated_at', 
                       'users.id as user_id', 'users.name as user_name', 'users.email as user_email', 'users.status'
                       )
              ->where('markers.id', '=', $marker_id)
              ->where('user_markers.del_flg', '=', 0);

        return $query;
    }

    /**
     * コミュニティに属しているユーザデータの取得
     * 引数1：コミュニティID, 引数2: ユーザID(一覧を表示する場合は要省略)
     * 
     */
    public function getCommunityUserQuery($community_id, $user_id=null) {
        $query = $this->model()->query();
    
        $query->leftJoin('community_histories', 'users.id', 'community_histories.user_id')
              ->leftJoin('communities', 'communities.id', 'community_histories.community_id')
              ->select('users.*', 'communities.id as community_id', 'community_histories.status as entry_status', 
                       'community_histories.memo as entry_memo', 'community_histories.id as history_id')
              ->where('community_id', '=', $community_id)
              ->where('users.del_flg', '=', 0);

        // ユーザ情報の詳細を取得する際に設定
        if(!is_null($user_id)) {
            $query->where('users.id', '=', $user_id);
        }
              
        return $query;
    }

    /**
     * ユーザー論理削除時のメールアドレスnull更新
     * @param $model
     */
    public function removeUserEmail($model) {
        $model->del_flg = 1;
        $model->memo = $model->memo.":old-email[".$model->email."]";
        $model->email = null;
        $model->save();
    }
}