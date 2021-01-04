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
    public function getNewsQuery($order=[], $limit=0) {
        // newsテーブルからデータを取得
        $query = $this->searchQuery([], [], [], $limit)->select('title', 'body', 'image_file', 'condition_start_time', 'condition_end_time');

        // ソート条件
        foreach($order as $key => $value) {
            switch ($value) {
                // 作成日時の昇順
                case 99:
                    $query->orderBy('news.created_at', 'asc');
                break;
                // 作成日時の降順
                case -99:
                    $query->orderBy('news.created_at', 'desc');
                break;
                
            }
        }

        return $query;
    }
}