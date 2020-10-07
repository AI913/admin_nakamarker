<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PointsGiftHistory extends BaseModel
{
    /**
     * 保存対象(attribute)
     * @var array
     */
    protected $fillable = [
        'give_point', 'charge_flg', 'give_user_id', 'take_user_id', 'status', 'user_points_history_id', 'memo'
    ];
}
