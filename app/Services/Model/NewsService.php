<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\News;

class NewsService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(News $model) {
        $this->model = $model;
    }

}