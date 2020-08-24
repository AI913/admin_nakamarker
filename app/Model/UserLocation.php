<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\User;
use App\Model\Marker;
use Illuminate\Support\Facades\DB;

class UserLocation extends BaseModel
{
    // usersテーブルと1対1のリレーション構築
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }
    // markersテーブルと1対1のリレーション構築
    public function marker() {
        return $this->belongsTo('App\Model\Marker', 'marker_id', 'id');
    }
}
