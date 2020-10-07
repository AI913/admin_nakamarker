<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Model\PointsGiftHistory;

class PointsGiftHistoryService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(PointsGiftHistory $model) {
        $this->model = $model;
    }

    /**
     * データの形式変更
     * 引数：保存データ
     */
    public function saveBefore($data) {
        return $input = [
            'give_point'                => $data['give_point'],
            'charge_flg'                => $data['charge_flg'],
            'give_user_id'              => $data['update_user_id'],
            'take_user_id'              => $data['user_id'],
            'status'                    => 2,
            'user_points_history_id'    => $data['user_points_history_id'],
            'update_user_id'            => $data['update_user_id'],
        ];
    }
}