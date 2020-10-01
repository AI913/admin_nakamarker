<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PushHistory extends BaseModel
{
    /**
     * 独自アクセサ(attribute)
     * @var array
     */
    protected $child_appends = [
        'type_name',
        'status_name',
        // 'send_date_style',
        'reservation_date_style'
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
     * プッシュ通知の種別名を返す
     * @return string
     */
    public function getTypeNameAttribute() {
        if ($this->type == config('const.push_all'))  return config('const.push_all_name');
        if ($this->type == config('const.push_condition'))     return config('const.push_condition_name');
    }

    /**
     * プッシュ通知のステータス名を返す
     * @return string
     */
    public function getStatusNameAttribute() {
        if ($this->status == config('const.push_before'))  return config('const.push_before_name');
        if ($this->status == config('const.push_now'))     return config('const.push_now_name');
        if ($this->status == config('const.push_after'))   return config('const.push_after_name');
        if ($this->status == config('const.push_error'))   return config('const.push_error_name');
    }

    /**
     * 時刻表記(send_date)
     * @return string
     */
    // public function getSendDateStyleAttribute() {
    //     return date("Y年m月d日 H時i分" , strtotime($this->send_date));
    // }
    /**
     * 時刻表記(reservation_date)
     * @return string
     */
    public function getReservationDateStyleAttribute() {
        return date("Y年m月d日 H時i分" , strtotime($this->reservation_date));
    }
}
