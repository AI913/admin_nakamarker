<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserPointsHistory extends BaseModel
{
    /**
     * 保存対象(attribute)
     * @var array
     */
    protected $fillable = [
        'give_point', 'pay_point', 'limit_date', 'user_id'
    ];

    /**
     * 独自アクセサ(attribute)
     * @var array
     */
    protected $child_appends = [
        'type_name', 'charge_name'
    ];

    /**
     * コンストラクタ
     * @param array $attributes
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        // 親のアクセサと子のアクセサ配列をマージ
        $this->appends = array_merge($this->child_appends, $this->appends);
    }

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
        if ($this->charge_flg == config('const.charge_flg_off'))  return config('const.charge_flg_off_name');
        if ($this->charge_flg == config('const.charge_flg_on'))     return config('const.charge_flg_on_name');
    }

    // usersテーブルと1対多のリレーション構築
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }
}
