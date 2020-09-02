<?php

namespace App\Model;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * 独自アクセサ(attribute)
     * @var array
     */
    protected $appends = ['status_name', 'entry_status_name'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'memo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 内部リレーション
     */
    public function parentUser()
    {
        return $this->hasOne(User::class, 'id', 'update_user_id');
    }

    /**
     * 管理画面権限があるかどうか
     * @return bool
     */
    public function isAdmin() {
        if ($this->status == 3) {
            return true;
        }
        return false;
    }

    /**
     * ステータス名を返す
     * (数値からconst.phpで定義した文字列へと変換)
     * @return string
     */
    public function getStatusNameAttribute() {
        if ($this->status == config('const.user_app_member'))  return config('const.user_app_member_name');
        if ($this->status == config('const.user_app_unsubscribe'))     return config('const.user_app_unsubscribe_name');
        if ($this->status == config('const.user_admin_system'))   return config('const.user_admin_system_name');
        if ($this->status == config('const.user_app_account_stop'))   return config('const.user_app_account_stop_name');
    }

    /**
     * コミュニティの申請状況を返す
     * (数値からconst.phpで定義した文字列へと変換)
     * @return string
     */
    public function getEntryStatusNameAttribute() {
        if ($this->entry_status == config('const.community_history_apply'))  return config('const.community_history_apply_name');
        if ($this->entry_status == config('const.community_history_approval'))     return config('const.community_history_approval_name');
        if ($this->entry_status == config('const.community_history_reject'))   return config('const.community_history_reject_name');
    }

    // communitiesテーブルと多対多のリレーション構築
    public function community()
    {
        return $this->belongsToMany('App\Model\Community');
    }
    // user_locationsテーブルと1対1のリレーション構築
    public function userLocation()
    {
        return $this->hasOne('App\Model\UserLocation', 'id', 'user_id');
    }
    // community_locationsテーブルと1対1のリレーション構築
    public function communityLocation()
    {
        return $this->hasOne('App\Model\CommunityLocation', 'id', 'user_id');
    }
    // user_points_historyテーブルと1対多のリレーション構築
    public function user_point_history()
    {
        return $this->hasMany('App\Model\UserPointsHistory', 'id', 'user_id');
    }
    // newsテーブルと1対多のリレーション構築
    public function news()
    {
        return $this->hasMany('App\Model\News', 'id', 'update_user_id');
    }
}
