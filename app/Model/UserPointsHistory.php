<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserPointsHistory extends Model
{
    protected $fillable = [
        'give_point', 'pay_point', 'limit_date', 'user_id'
    ];

    /**
     * ポイントの付与種別を返す
     * @return string
     */
    public function getTypeNameAttribute() {
        if ($this->type == config('const.point_buy'))  return config('const.point_buy_name');
        if ($this->type == config('const.point_gift'))     return config('const.point_gift_name');
        if ($this->type == config('const.point_advertise'))   return config('const.point_advertise_name');
        if ($this->type == config('const.point_admin'))   return config('const.point_admin_name');
    }

    /**
     * ポイントの有料フラグを返す
     * @return string
     */
    public function getChargeNameAttribute() {
        if ($this->type == config('const.charge_flg_off'))  return config('const.charge_flg_off_name');
        if ($this->type == config('const.charge_flg_on'))     return config('const.charge_flg_on_name');
    }

    // usersテーブルと1対多のリレーション構築
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }
}
