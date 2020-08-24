<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserPointsHistory extends Model
{
    protected $fillable = [
        'give_point', 'pay_point', 'limit_date', 'user_id'
    ];

    // usersテーブルと1対1のリレーション構築
    public function user()
    {
        return $this->hasOne('App\Model\User', 'user_id', 'id');
    }
}
