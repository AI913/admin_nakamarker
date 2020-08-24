<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\Marker;

class MarkerService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(Marker $model) {
        $this->model = $model;
    }

}