<?php
namespace App\Services\Api;

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

    /**
     * 全ニュースデータを取得
     * 引数1：ソート条件, 引数2：取得件数
     */
    public function getNewsQuery($order=[], $limit=0, $offset=0) {
      return $this->searchQuery([], $order, [], $limit, $offset)
                  ->select('id', 'title', 'body', 'image_file', 'condition_start_time', 'condition_end_time')
                  ->get();
    }
}
