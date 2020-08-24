<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\Config;

class ConfigService extends BaseService
{
    // 店舗リストキャッシュキー
    const CACHE_KEY = "cache_config_list";
    // 店舗リストキャッシュ時間(分)
    const CACHE_TIME = 60 * 24;
    
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(Config $model) {
        $this->model = $model;
    }

    /**
     * 共通設定のキーが存在するかどうかチェックする
     * @param unknown $config_key
     * @param number $config_id
     * @return mixed
     */
    public function isConfigForKey($config_key, $config_id=0) {
        $conditions["key"] = $config_key;
        // 店舗IDが指定されていれば除外
        if ($config_id) {
            $conditions["configs.id@not"] = $config_id;
        }
        return $this->searchExists($conditions);
    }

    /**
     * キー指定配列取得
     * @return array|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getKeyList() {
        // キャッシュがあればそちらを使う
        $list = json_decode(\Cache::get(self::CACHE_KEY));
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
        \Cache::delete(self::CACHE_KEY);

        $list = [];
        foreach($this->all() as $value) {
            $list[$value->key] = $value->value;
        }
        // キャッシュに保存する
        \Cache::put(self::CACHE_KEY, json_encode($list), self::CACHE_TIME);

        return json_decode(\Cache::get(self::CACHE_KEY));
    }
}