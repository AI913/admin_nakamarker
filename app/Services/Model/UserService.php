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
     * ポイント履歴から現在のポイント数を算出
     */
    public function getPointData() {
        $query = $this->model()->query();

        $query
              ->leftJoin('user_points_histories', 'users.id', 'user_points_histories.user_id')
              ->selectRaw('sum(user_points_histories.give_point) - sum(user_points_histories.pay_point) as total_points')
              ->groupByRaw('user_points_histories.user_id');

        return $query;
    }

    /**
     * コミュニティ一覧ページに表示する参加ユーザデータを取得
     */
    public function isCommunityUserData($community_id) {
        $query = $this->model()->query();

        // ユーザごとに現在の所有ポイントを算出
        $points_query = DB::table('user_points_histories up')->select('sum(up.give_point) - sum(up.pay_point) as total_points', 'up.user_id')
                                                             ->groupByRaw('up.user_id');
        // $points_query = DB::Raw('select p.user_id, sum(p.give_point) - sum(p.pay_point) as total_points from user_points_histories p group by p.user_id');

        // サブクエリでポイントテーブルとユーザテーブルを結合
        $query->leftJoin('community_histories', 'users.id', 'community_histories.user_id')
              ->leftJoin('communities', 'community_histories.community_id', 'communities.id')
            //   ->leftJoin($points_query.' as points', 'users.id', '=', 'points.user_id')
            //   ->select('users.*', 'community_histories.updated_at', 'points.total_points')
              ->select('users.*', 'community_histories.updated_at')
              ->where('community_histories.community_id', '=', $community_id)
              ->where('community_histories.status', '=', config('const.community_history_approval'));

        return $query;
    }
}