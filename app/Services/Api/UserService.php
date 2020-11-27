<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
     * ユーザートークン発行　md5(ランダム＋id)
     * @param $id
     * @return string
     */
    public function issueUserToken($id) {
        return md5(Str::random(64).$id);
    }

    /**
     * ワンタイムパスワード発行
     *   ※12文字で設定(大文字英数字で表示)
     *   ※1とI、0とOは設定から省く
     * @param $id
     * @return string
     */
    public function issueOnetimePassword() {

        // パスワード発行に利用する文字列と数字の配列を用意
        $str_list = range('A', 'Z');
        $str_list = array_diff($str_list, array('I', 'O')); // パスワードの除外文字を設定

        $number_list = range(1, 9);
        $number_list = array_diff($number_list, array(1)); // パスワードの除外文字を設定

        // パスワード発行用の文字と数字を結合
        $password_list = array_merge($str_list, $number_list);

        // パスワードの発行
        $password = '';
        for($i=0; $i<12; $i++) {
            $password .= $password_list[array_rand($password_list)];
        }
        
        return $password;
    }

    /**
     * ポイント履歴から現在の無料ポイント数を算出
     * 引数：検索条件
     */
    public function getFreePointQuery($conditions=null) {
        $query = $this->model()->query();

        $query->leftJoin('user_points_histories', 'users.id', 'user_points_histories.to_user_id')
              ->selectRaw('sum(user_points_histories.give_point) - sum(user_points_histories.pay_point) as free_total_points')
              ->addselect('user_points_histories.to_user_id')
              ->groupByRaw('user_points_histories.to_user_id')
              ->where('user_points_histories.charge_flg', '=', 1)
              ->where('user_points_histories.del_flg', '=', 0);

        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }

        return $query;
    }

    /**
     * ポイント履歴から現在の有料ポイント数を算出
     * 引数：検索条件
     */
    public function getPointQuery($conditions=null) {
        $query = $this->model()->query();

        $query->leftJoin('user_points_histories', 'users.id', 'user_points_histories.to_user_id')
              ->selectRaw('sum(user_points_histories.give_point) - sum(user_points_histories.pay_point) as total_points')
              ->addselect('user_points_histories.to_user_id')
              ->groupByRaw('user_points_histories.to_user_id')
              ->where('user_points_histories.charge_flg', '=', 2)
              ->where('user_points_histories.del_flg', '=', 0);

        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }
        
        return $query;
    }
}