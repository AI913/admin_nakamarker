<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\UserLocation;

class UserLocationService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(UserLocation $model) {
        $this->model = $model;
    }

}