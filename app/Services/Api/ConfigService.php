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
     */
    public function getConfigDataQuery() {
        // 削除フラグ排除のため、searchQuery()を実行
        $query = $this->searchQuery()
                      ->select('key', 'value');

        return $query;
    }
}