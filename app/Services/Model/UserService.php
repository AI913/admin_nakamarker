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
    public function getFreePointData() {
        $query = $this->model()->query();

        $query->leftJoin('user_points_histories', 'users.id', 'user_points_histories.user_id')
              ->selectRaw('sum(user_points_histories.give_point) - sum(user_points_histories.pay_point) as free_total_points')
              ->addselect('user_points_histories.user_id')
              ->groupByRaw('user_points_histories.user_id')
              ->where('user_points_histories.charge_flg', '=', 1);

        return $query;
    }

    /**
     * ポイント履歴から現在の有料ポイント数を算出
     */
    public function getPointData() {
        $query = $this->model()->query();

        $query->leftJoin('user_points_histories', 'users.id', 'user_points_histories.user_id')
              ->selectRaw('sum(user_points_histories.give_point) - sum(user_points_histories.pay_point) as total_points')
              ->addselect('user_points_histories.user_id')
              ->groupByRaw('user_points_histories.user_id')
              ->where('user_points_histories.charge_flg', '=', 2);

        return $query;
    }

    /**
     * ユーザの所持ポイントを算出したデータの取得
     * 引数：データの検索条件
     */
    public function isUserPointData($conditions=null) {
        $query = $this->model()->query();

        // 無料ポイントと有料ポイントの算出クエリをそれぞれインスタンス化
        $free_points_query = $this->getFreePointData();
        $points_query = $this->getPointData();

        // サブクエリでポイントテーブルとユーザテーブルを結合
        $query->leftJoinSub($free_points_query, 'free_points', 'users.id', '=', 'free_points.user_id')
              ->leftJoinSub($points_query, 'charge_points', 'users.id', '=', 'charge_points.user_id')
              ->select('users.*', 'free_points.free_total_points', 'charge_points.total_points')
              ->orderBy('users.id');

        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }
        return $query;
    }

    /**
     * コミュニティに属しているユーザデータの取得
     * 引数：コミュニティID
     * 
     */
    public function isCommunityUserData($community_id) {
        $query = $this->model()->query();
    
        $query->leftJoin('community_histories', 'users.id', 'community_histories.user_id')
              ->leftJoin('communities', 'communities.id', 'community_histories.community_id')
              ->select('users.*', 'communities.id as community_id', 'community_histories.status as entry_status')
              ->where('community_id', '=', $community_id);
              
        return $query;
    }
}