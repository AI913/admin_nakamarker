<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class BaseService{

    protected $model;

    /**
     * メインModel
     * @return Model
     */
    public function model(): Model {
        return $this->model;
    }

    /**
     * 削除フラグあり・なし※デフォルトあり
     * @return bool
     */
    public function is_del_flg() {
        return true;
    }
    /**
     * Model新規インスタンス生成
     * @return Model
     */
    public function newModel() {
        return new $this->model;
    }
    /**
     * 全件検索(論理削除されていないもの)
     * @return BaseModel[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        return $this->model()::query()->where('del_flg', 0)->get();
    }
    /**
     * 指定ID検索
     * @param $id
     * @return mixed
     */
    public function find($id) {
        return $this->model()::query()->where('id', $id)->first();
    }

    /**
     * テーブルの件数取得
     * @param bool $del_flg
     * @return int
     */
    public function count($del_flg = true) {
        if ($del_flg) {
            return $this->model()::query()->where('del_flg', 0)->count();
        }
        return $this->model()::query()->count();
    }
    /**
     * テーブルに指定IDデータが存在するかどうか
     * @param $id
     * @return mixed
     */
    public function exists($id) {
        return $this->model()::query()->where('id', $id)->exists();
    }
    /**
     * 指定条件で検索し、存在するかどうか
     * @param array $conditions
     * @param array $order
     * @param array $relation
     * @return mixed
     */
    public function searchExists($conditions=[], $order=[], $relation=[]) {
        return $this->searchQuery($conditions, $order, $relation)->exists();
    }
    /**
     * 指定条件で検索し、1件取得
     * @param array $conditions
     * @param array $order
     * @param array $relation
     * @return mixed
     */
    public function searchOne($conditions=[], $order=[], $relation=[]) {
        return $this->searchQuery($conditions, $order, $relation)->first();
    }

    /**
     * 指定条件で検索し、リストで取得
     * @param array $conditions
     * @param array $order
     * @param array $relation
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function searchList($conditions=[], $order=[], $relation=[], $limit=0) {
        return $this->searchQuery($conditions, $order, $relation, $limit)->get();
    }
    /**
     * 指定条件で検索し、件数を取得
     * @param array $conditions
     * @param array $order
     * @param array $relation
     * @return int
     */
    public function searchCount($conditions=[], $order=[], $relation=[]) {
        return $this->searchQuery($conditions, $order, $relation)->count();
    }

    /**
     * 指定条件での検索(複数取得)
     * @param array $conditions(検索条件)
     *      (例)：　$conditions[] = ['key' => $value]
     *             key は条件によって、オプション付与可能(カラム@条件)
     *              (例)： 'name@like' @likeを付与し、like検索が可能
     * @param array $order(ソート条件)
     *      (例)：　$order[] = ['key' => 'desc' or 'asc']
     * @param array $relation(リレーション先名称)
     *      (例)：　$relation[] = ['store' => [], 'user' => ['del_flg' => 0]]
     *              リレーション先テーブルに条件を加える場合は、下記参照
     * @param int $limit(取得件数)
     * @param int $offset(取得開始するindex)
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function searchQuery($conditions=[], $order=[], $relation=[], $limit=0, $offset=0) {
        $query = $this->model()::query();

        // 検索条件
        $query = self::getConditions($query, $this->model()->getTable(), $conditions);
        // リレーション条件
        foreach($relation as $key => $condition) {
            // リレーション
            $query->with($key);
            // 条件なしの場合は通常のリレーションと同じ
            if ($condition) {
                // リレーション先条件指定
                $query->whereIn($condition['owner_key'], function ($query) use($condition) {
                    $query->select($condition['foreign_key'])->from($condition['table']);
                    self::getConditions($query, $condition['table'], $condition['condition'], false);
                });
            }
        }
        // ソート条件
        foreach($order as $key => $value) {
            // カスタムオーダーの場合
            if (preg_match('/@custom/', $key)) {
                // 文字列をorder by節の値として指定するために使用
                $query->orderByRaw($value);
            } else {
                $query->orderBy($key, $value);
            }
        }
        // 件数指定あれば設定
        if ($offset > 0) {
            $query->offset($offset);
        }
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query;
    }

    /**
     * 指定データ保存
     * @param $data 保存するデータ
     * @return Model|mixed 保存したモデル
     * @throws \Exception
     */
    public function save($data) {
      try {
        $now = Carbon::now();

        if (isset($data['id'])) {
          $model = $this->find($data["id"]);
        } else {
          $model = $this->newModel();
          $model->created_at = $now;
        }

        $model->update_user_id = self::getUserId();
        $model->updated_at = $now;
        $model->fill($data);
        $model->save();

        return $model;
      } catch (\Exception $e) {
        \Log::error('database save error:'.$e->getMessage());
        throw new \Exception($e);
        }
    }

    /**
     * 指定IDデータ削除(論理削除)
     * @param $id
     * @param bool $common
     * @param bool $transaction
     * @return mixed
     * @throws \Exception
     */
    public function remove($id, $common=true, $transaction=true) {
        if ($transaction) \DB::beginTransaction();
        try {
            $model = $this->find($id);
            if ($common) {
                // 共通DBレイアウトの場合
                $model->del_flg = 1;
                $model->updated_at = Carbon::now();
                $model->update_user_id = self::getUserId();
            }
            $model->save();
            if ($transaction) \DB::commit();
            return $model;
        } catch (\Exception $e) {
            if ($transaction) \DB::rollBack();
            \Log::error('database remove error:'.$e->getMessage());
            throw new \Exception($e);
        }
    }

    /**
     * 検索条件作成
     * @param $query
     * @param $table
     * @param array $conditions
     * @param bool $del_flg
     * @return mixed
     */
    protected function getConditions($query, $table, $conditions=[], $del_flg = true) {
        $table = $table.".";

        foreach($conditions as $key => $value) {
            if (preg_match('/@like/', $key)) {
                // LIKE検索
                $query->where(str_replace("@like", "", $key), 'like', '%'.$value.'%');
            } else if (preg_match('/@not/', $key)) {
                // NOT検索
                $query->where(str_replace("@not", "", $key), '!=', $value);
            } else if (preg_match('/@>=/', $key)) {
                // 大なりイコール
                $query->where(str_replace("@>=", "", $key), '>=', $value);
            } else if (preg_match('/@<=/', $key)) {
                // 小なりイコール
                $query->where(str_replace("@<=", "", $key), '<=', $value);
            } else if (preg_match('/@</', $key)) {
                // 大なり
                $query->where(str_replace("@<", "", $key), '<', $value);
            } else if (preg_match('/@>/', $key)) {
                // 小なり
                $query->where(str_replace("@>", "", $key), '>', $value);
            } else if (preg_match('/@in/', $key)) {
                // IN
                $query->whereIn(str_replace("@in", "", $key), $value);
            } else if (preg_match('/@not_in/', $key)) {
                // NotIN
                $query->whereNotIn(str_replace("@not_in", "", $key), $value);
            } else if (preg_match('/@and_or/', $key)) {
                // And-OR
                // ※この場合のみ「value」部分は「value1==value2」と指定すること
                $values = explode('==', $value);
                $query->where(function($query) use($key, $values) {
                    foreach($values as $val) {
                        // ※判定値がnullの場合はWhereNullで判定
                        if ($val == "null") {
                            $query->orWhereNull(str_replace("@and_or", "", $key));
                        } else {
                            $query->orWhere(str_replace("@and_or", "", $key), $val);
                        }
                    }
                });
            } else if (preg_match('/@is_null/', $key)) {
                // Is Null
                $query->whereNull(str_replace("@is_null", "", $key));
            } else if (preg_match('/@is_not_null/', $key)) {
                // Is Not Null
                $query->whereNotNull(str_replace("@is_not_null", "", $key));
            } else if (preg_match('/@custom/', $key)) {
                // カスタム条件
                $query->whereRaw($value);
            } else {
                // 通常検索
                $query->where($key, $value);
            }
        }
        // 条件に削除フラグがない場合強制的に付与
        if ($this->is_del_flg() && $del_flg) {
            $query->where($table.'del_flg', 0);
    }
        return $query;
    }
    /**
     * ログイン中ユーザーID取得
     * @return \Illuminate\Contracts\Auth\Authenticatable|int|null
     */
    private function getUserId() {
        if (request()->bearerToken()) {
            $user = \DB::table('users')->select('id')->where('user_token', request()->bearerToken())->first();
            return $user->id;
        }
        return 0;
    }
}
