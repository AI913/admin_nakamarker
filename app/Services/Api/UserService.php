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
              ->where('user_points_histories.charge_type', '=', config('const.charge_type_off'))
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
              ->where('user_points_histories.charge_type', '=', config('const.charge_type_on'))
              ->where('user_points_histories.del_flg', '=', 0);

        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }
        
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
        $query->leftJoinSub($free_points_query, 'free_points', 'users.id', '=', 'free_points.to_user_id')
              ->leftJoinSub($points_query, 'charge_points', 'users.id', '=', 'charge_points.to_user_id')
              ->select('users.*', 'free_points.free_total_points', 'charge_points.total_points');

        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }
        return $query;
    }

    /**
     * ログインユーザが所有するマーカーリスト
     * 引数1：検索条件, 引数2：ソート条件 
     */
    public function getUserMarkerQuery($conditions=[], $order=[]) {
        // 削除フラグ排除のため、searchQuery()を実行
        $query = $this->searchQuery($conditions)
                      ->select('id')
                      ->with(['marker' => function ($query) {
                          // user_markersテーブルとmarkersテーブルの値をクエリすることが可能
                          $query->select('user_markers.marker_id', 'user_markers.updated_at', 
                                         'markers.type', 'markers.name', 'markers.image_file', 'markers.description');
                      }])
                      ->get();
        // 結合したmarkersテーブルと中間テーブルの値に絞り込み
        $query = $query[0]->marker;
        
        // ソート条件
        foreach($order as $key => $value) {
            switch ($key) {
                // 作成日時の昇順
                case 99:
                    $query = $query->sortBy('created_at');
                break;
                // 作成日時の降順
                case -99:
                    $query = $query->sortByDesc('created_at');
                break;
                // マーカーの種別で昇順
                case 1:
                    $query = $query->sortBy('type');
                break;
                // マーカーの種別で降順
                case -1:
                    $query = $query->sortByDesc('type');
                break;
            }
        }
        return $query;
    }

    /**
     * ログインユーザが所属するコミュニティリスト
     * 引数1：検索条件, 引数2：ソート条件 
     */
    public function getUserCommunityQuery($conditions=[], $order=[]) {
        // 削除フラグ排除のため、searchQuery()を実行
        $query = $this->searchQuery($conditions)
                      ->select('id')
                      ->with(['community' => function ($query) {
                          // community_historiesテーブルとcommunitiesテーブルの値をクエリすることが可能
                          $query->select('communities.id as community_id', 'communities.type', 'communities.name',
                                         'communities.description', 'communities.image_file',
                                         'communities.status as community_status',
                                         'community_histories.status as entry_status',
                                         'host_user_id');
                      }])
                      ->get();

        // 結合したcommunitiesテーブルと中間テーブルの値に絞り込み
        $query = $query[0]->community;
        
        // ソート条件
        foreach($order as $key => $value) {
            switch ($key) {
                // 作成日時の昇順
                case 99:
                    $query = $query->sortBy('created_at');
                break;
                // 作成日時の降順
                case -99:
                    $query = $query->sortByDesc('created_at');
                break;
                // コミュニティの種別で昇順
                case 1:
                    $query = $query->sortBy('type');
                break;
                // コミュニティの種別で降順
                case -1:
                    $query = $query->sortByDesc('type');
                break;
                // コミュニティの公開フラグで昇順
                case 2:
                    $query = $query->sortBy('community_status');
                break;
                // コミュニティの公開フラグで降順
                case -2:
                    $query = $query->sortByDesc('community_status');
                break;
                // コミュニティの申請ステータスで昇順
                case 3:
                    $query = $query->sortBy('entry_status');
                break;
                // コミュニティの申請ステータスで降順
                case -3:
                    $query = $query->sortByDesc('entry_status');
                break;
            }
        }

        // 自身がオーナーであるコミュニティにはフラグを立てる
        foreach($query as $communityData) {
          $communityData['isHost'] = ($communityData['host_user_id'] == $conditions['id']);
        }

        return $query;
    }

    /**
     * ユーザの新規作成
     * @param $data ユーザデータ
     * @return Model|mixed
     * @throws \Exception
     */
    public function create($data) {
      try {
        $now = Carbon::now();
        $model = $this->newModel();
        $model->fill($data);
        $model->login_time = $now;
        $model->created_at = $now;
        $model->updated_at = $now;
        $model->save();
        $id = $model->id;
        $model->user_token = self::issueUserToken($id);
        $model->update_user_id  = $id;
        $model->save();
        return $model;
      } catch (\Exception $e) {
        \Log::error('database save error:'.$e->getMessage());
        throw new \Exception($e);
      }
    }
}
