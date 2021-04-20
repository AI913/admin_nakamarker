<?php
namespace App\Services\Api;

use Illuminate\Support\Facades\Cache;
use App\Model\Config;

class ConfigService extends BaseService
{
    // 共通設定リストキャッシュ時間(分)
    const CACHE_TIME = 60 * 24;

    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(Config $model)
    {
        $this->model = $model;
    }

    /**
     * 共通設定データのキーと値のみをリターン
     */
    public function getConfigDataQuery() {
        // 削除フラグ排除のため、searchQuery()を実行
        $query = $this->searchQuery()
                      ->select('key', 'value');

        return $query;
    }

    /**
     * キー指定配列取得
     * @return array|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getKeyList() {
        // キャッシュがあればそちらを使う
        $list = json_decode(Cache::get(config('const.config_cache_key')));
        if ($list) {
            return $list;
        }
        // なければ再生成
        return $this->createCache();
    }

    /**
     * 指定キーの値を取得
     * @param $key
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getKeyValue($key) {
        $list = $this->getKeyList();
        return $list->$key;
    }

    /**
     * システム設定値キャッシュ処理
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function createCache() {

        // キャッシュクリア
        Cache::delete(config('const.config_cache_key'));

        $list = [];
        foreach($this->all() as $value) {
            $list[$value->key] = $value->value;
        }
        // キャッシュに保存する
        Cache::put(config('const.config_cache_key'), json_encode($list), self::CACHE_TIME);

        return json_decode(Cache::get(config('const.config_cache_key')));
    }
}