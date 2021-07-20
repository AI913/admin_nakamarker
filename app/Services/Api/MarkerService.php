<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\Marker;

class MarkerService extends BaseService
{
    /**
     * コンストラクタ
     * MarkerService constructor.
     */
    public function __construct(Marker $model) {
        $this->model = $model;
    }

    /**
     * マーカーの一覧データを取得
     * 引数1：検索条件 引数2：ソート条件
     */
    public function getMarkerQuery($conditions=[], $order=[]) {
      return $this->searchQuery($conditions, $order)
                  ->select('id as marker_id', 'type', 'name', 'search_word', 'description', 'price', 'charge_type', 'status')
                  ->get();
    }
}
