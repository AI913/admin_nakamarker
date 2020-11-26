<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Model\Config;

class ConfigService extends BaseService
{
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
     * 引数：検索条件
     */
    public function getConfigDataQuery($conditions=null) {
        $query = $this->model::query();
        
        $query->select('key', 'value')
              ->where('del_flg', '=', 0);

        // 検索条件がある場合は検索を実行
        if ($conditions) {
            $query = $query->getConditions($query, $this->model->getTable(), $conditions);
        }

        return $query;
    }
}