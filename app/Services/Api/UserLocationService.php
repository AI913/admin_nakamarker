<?php
namespace App\Services\Api;

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

    /**
     * ユーザ一覧ページに表示する登録場所データを取得
     * 引数1: 検索条件, 引数2: ソート条件
     */
    public function getUserLocationQuery($conditions=[], $order=[]) {
      return $this->searchQuery($conditions, $order)
                  ->select('id as location_id', 'name as location_name', 'latitude', 'longitude', 'image_file', 'memo', 'marker_id')
                  ->with('marker:id,name as marker_name,type as marker_type')
                  ->get();
    }
}
