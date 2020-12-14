<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Model\UserPointsHistory;
use App\Services\Api\UserService;

class UserPointsHistoryService extends BaseService
{
    protected $userService;

    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(UserPointsHistory $model, UserService $userService) {
        $this->model = $model;

        $this->userService = $userService; 
    }

    /**
     * 有効期限の最も近いポイントを取得
     * 引数: 検索条件
     */
    public function getLimitDateBaseQuery($conditions) {
        $query = $this->model()::query();

        $query = $query->selectRaw('id, limit_date, give_point - pay_point as remaining_points');

        // 検索条件があれば実行
        if($conditions) {
            $query = $this->getConditions($query, $this->model()->getTable(), $conditions);
        }
        
        return $query;
    }

    /**
     * データの変換処理
     * 引数1: データ, 引数2: ポイント区分
     */
    public function getDataChangeQuery($data, $type) {
        // 有料ポイントの合計値データのみをリターン
        if(!is_null($data) && $type == 2) {
            return $data->total_points;
        }
        // 無料ポイントの合計値データのみをリターン
        if(!is_null($data) && $type == 1) {
            return $data->free_total_points;
        }
        // nullの場合は0をリターン
        if($data == null) {
            $data = 0;
        }
        // それ以外はそのままリターン
        return $data;
    }

    /**
     * ポイントの使用可否確認処理
     * 引数1: ユーザID, 引数2: 消費ポイント数, 引数3: ポイント区分
     */
    public function getConfirmPointQuery($user_id, $pay_points, $type) {

        // 現在の所有ポイント数(有料)合計を算出
        $charge = $this->userService->getPointQuery()
                                    ->where('user_points_histories.to_user_id', '=', $user_id)
                                    ->first();
        // nullチェック
        $charge = $this->getDataChangeQuery($charge, 2);

        // ポイント区分が"有料"の場合
        if($type == 2) {
            // 所有する有料ポイントの値が消費ポイントより低い場合はfalseをリターン
            if($charge < $pay_points) {
                return false;
            }
        }

        // ポイント区分が"無料"の場合
        if($type == 1) {
            // 現在の所有ポイント数(無料)合計を算出
            $free = $this->userService->getFreePointQuery()
                                      ->where('user_points_histories.to_user_id', '=', $user_id)
                                      ->first();
            // nullチェック
            $free = $this->getDataChangeQuery($free, 1);

            // 無料ポイントと有料ポイントの合計値を変数に代入
            $total_points = (int)$charge + (int)$free;
            
            // 所有する合計ポイントが消費ポイントより低い場合はfalseをリターン
            if($total_points < $pay_points) {
                return false;
            }
        }
        return true;
    }

    /**
     * ポイントの消費処理
     * 引数1: 消費ポイント数, 引数2: ポイント履歴データ, 引数3: ポイント区分
     */
    protected function getPayAction($pay_points, $data, $type) {
        $current_pay_points = null;
        $tmp = null;
        $loop_time = 0;
        
        foreach($data as $value) {
            // 指定したポイント区分と異なる場合は消費処理をスキップ
            if($value->charge_flg != $type) {
                continue;
            }
            // ループ処理の回数をカウント
            $loop_time++;
            // 現在のポイント履歴データ(1レコード)に残っているポイントを算出
            $current_points = $value->give_point - $value->pay_point;
            
            // 消費ポイントの残量を算出(ループ処理が2回目以降)
            if (isset($current_pay_points)) {
                // 計算結果を一時的に変数へ代入
                $tmp = $current_pay_points - $current_points;
                // 消費ポイントが保存しきれない場合は計算結果を反映させる
                // ※保存しきれる場合はDBに保存した後に計算結果を反映させる
                if ($tmp > 0) {
                    $current_pay_points = $tmp;
                }
            }
            // 消費ポイントの残量を算出(ループ処理が1回目のとき)
            if (!isset($current_pay_points)) {
                $current_pay_points = $pay_points - $current_points;
            }

            // 消費ポイントを保存できる、かつループ処理が1回目の場合
            if($current_pay_points <= 0 && $loop_time == 1) {
                // 保存後に処理を抜け出す
                $this->payPointSave($pay_points, $value->id);
                break;
            }
            // 消費ポイントを保存できる、かつループ処理が2回目以上の場合
            if($tmp <= 0 && $loop_time >= 2) {
                // 保存後に処理を抜け出す
                $this->payPointSave($current_pay_points, $value->id);
                // 計算結果を一時変数から$current_pay_pointsに反映
                $current_pay_points = $tmp;
                break;
            }
            // 消費ポイントを保存しきれない場合
            if($current_pay_points > 0) {
                // 保存して次の処理へ
                $this->payPointSave($current_points, $value->id);
                continue;
            }
        }
        return $current_pay_points;
    }

    /**
     * ポイント消費データの保存
     * 引数1: 保存する消費ポイントデータ, 引数2: 更新する履歴データのID
     */
    protected function payPointSave($data, $id) {
        // UPDATE用にデータ取得
        $model = $this->find($id);

        // 保存するデータの符号を反転
        if($data < 0) {
            $data = -$data;
        }
            
        // 既存のpay_pointカラムの値と合算する
        $data += $model->pay_point;

        // $dataの値がgive_pointカラムの値を超過してしまう場合
        if($model->give_point < $data) {
            throw new \Exception("Cannot save because the data of pay_point exceed the data of give_point at the time of saving.");
            return;
        }

        // pay_pointカラムを更新
        $model->pay_point = $data;
        // 対象レコードの付与ポイントを使い切った場合は'使用済みフラグ'を更新
        if($model->give_point === $model->pay_point) {
            $model->used_flg = 1;
        }
        $model->save();
        return;
    }

    /**
     * ポイント消費のメイン処理
     * 引数1: ユーザID, 引数2: 消費ポイント数, 引数3: ポイント区分
     */
    public function getPayPointQuery($user_id, $pay_points, $type) {

        // 消費ポイントが所有ポイントを上回っていないかを確認
        if(!$this->getConfirmPointQuery($user_id, $pay_points, $type)) {
            return false;
        }
        
        // ポイント区分が無料で、現在無料ポイントがない場合
        if($type == 1 && !$this->searchExists(['charge_flg' => $type, 'used_flg' => 0, 'to_user_id' => $user_id])) {
            // ポイント区分を反転させる
            $type = 2;
        }

        // 検索条件とソート条件を設定
        $conditions = [
            'del_flg'  => 0,
            'to_user_id'  => $user_id,
            'used_flg' => 0, // 使用済みのポイントは利用しないように排除
        ];
        // ポイント区分が有料の場合
        if($type == 2) {
            // 有料のポイント履歴のみに絞る
            $conditions['charge_flg'] = 2;
        }
        $order = ['created_at' => 'asc'];
        // ポイント履歴データを取得
        $data = $this->searchList($conditions, $order);
        
        \DB::beginTransaction();
        try {
            // リターン用の変数を宣言
            $charge_points = 0; // 有料ポイント用
            $free_points = 0;   // 無料ポイント用

            // ポイントの消費処理(有料ポイント)
            if($type == 2) {
                $this->getPayAction($pay_points, $data, $type);
                // 有料ポイント
                $charge_points = $pay_points;
            }

            // ポイントの消費処理(無料ポイント)
            if($type == 1) {
                $current_points = $this->getPayAction($pay_points, $data, $type);
                // 無料ポイントを使い切った場合有料ポイントの消費に移る
                if($current_points > 0 && !$this->searchExists(['charge_flg' => $type, 'used_flg' => 0, 'to_user_id' => $user_id])) {
                    // 消費ポイントの残量を渡して再度消費処理を実行
                    $this->getPayAction($current_points, $data, 2);
                    $charge_points = $pay_points - $current_points;
                    $free_points = $pay_points - $charge_points;
                }
                // 無料ポイント用の変数に値がなければ消費ポイントの値をセット
                $free_points ? $free_points : $free_points = $pay_points;
            }

            \DB::commit();
            return [
                'charge_points' => $charge_points,
                'free_points'   => $free_points
            ];
        } catch(\Exception $e) {
            \DB::rollBack();
            
            \Log::error('pay point save error:'.$e->getMessage());
            throw new \Exception($e);
            return;
        }
    }
}