<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CommunityLocation extends BaseModel
{
    /**
     * 保存対象(attribute)
     * @var array
     */
    protected $fillable = ['name', 'latitude', 'longitude', 'image_file', 'memo', 'user_id', 'marker_id', 'community_id', 'del_flg'];

    // usersテーブルと1対1のリレーション構築
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }
    // markersテーブルと1対1のリレーション構築
    public function marker() {
        return $this->belongsTo('App\Model\Marker', 'marker_id', 'id');
    }
    // communitiesテーブルと1対1のリレーション構築
    public function community() {
        return $this->belongsTo('App\Model\Community', 'community_id', 'id');
    }
}
