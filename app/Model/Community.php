<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Community extends BaseModel
{
    /**
     * 保存対象(attribute)
     * @var array
     */
    protected $fillable = ['name', 'description', 'member', 'status', 'image_file', 'del_flg'];

    // usersテーブルと多対多のリレーション構築
    public function community()
    {
        return $this->belongsToMany('App\Model\User');
    }
    // user_locationsテーブルと1対1のリレーション構築
    public function userLocation()
    {
        return $this->hasOne('App\Model\UserLocation');
    }
    // community_locationsテーブルと1対1のリレーション構築
    public function communityLocation()
    {
        return $this->hasOne('App\Model\CommunityLocation');
    }
}
