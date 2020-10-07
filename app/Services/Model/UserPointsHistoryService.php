<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Model\UserPointsHistory;

class UserPointsHistoryService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(UserPointsHistory $model) {
        $this->model = $model;
    }
}