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
                    $query->orderBy('created_at', 'asc');
                break;
                // 作成日時の降順
                case -99:
                    $query->orderBy('created_at', 'desc');
                break;
                // タイトルで昇順
                case 1:
                    $query->orderBy('title', 'asc');
                break;
                // タイトルで降順
                case -1:
                    $query->orderBy('title', 'desc');
                break;
                // 公開開始日時で昇順
                case 2:
                    $query->orderBy('condition_start_time', 'asc');
                break;
                // 公開開始日時で降順
                case -2:
                    $query->orderBy('condition_start_time', 'desc');
                break;
                // 公開終了日時で昇順
                case 3:
                    $query->orderBy('condition_end_time', 'asc');
                break;
                // 公開終了日時で降順
                case -3:
                    $query->orderBy('condition_end_time', 'desc');
                break;
            }
        }

        return $query;
    }
}