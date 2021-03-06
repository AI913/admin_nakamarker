<?php

namespace App\Model;

use App\Lib\Common;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * 独自アクセサ(attribute)
     * @var array
     */
    protected $appends = ['image_url', 'status_name', 'entry_status_name', 'created_at_style', 'updated_at_style', 'login_time_style'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_token', 'firebase_uid', 'email', 'password', 'login_time', 'device_token', 
        'status', 'memo', 'user_agent', 'onetime_password', 'limit_date', 'image_file', 'del_flg'
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
     * 画像URL取得
     * @return string
     */
    public function getImageUrlAttribute() {
        return Common::getImageUrl($this->image_file, $this->table);
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

    /**
     * 時刻表記(created_at)
     * @return string
     */
    public function getCreatedAtStyleAttribute() {
        return date("Y年m月d日 H時i分" , strtotime($this->created_at));
    }
    /**
     * 時刻表記(updated_at)
     * @return string
     */
    public function getUpdatedAtStyleAttribute() {
        return date("Y年m月d日 H時i分", strtotime($this->updated_at));
    }
    /**
     * 時刻表記(login_time)
     * @return string
     */
    public function getLoginTimeStyleAttribute() {
        if(!is_null($this->login_time)) {
            return date("Y年m月d日 H時i分", strtotime($this->login_time));
        }
    }

    /**
     * communitiesテーブルと多対多のリレーション構築
     */
    public function community()
    {
        return $this->belongsToMany(
            'App\Model\Community',          // 結合先テーブル
            'community_histories',          // 中間テーブル名
            'user_id',                      // 中間テーブルにあるFK
            'community_id'                  // リレーション先モデルのFK
        );
    }
    /**
     * user_locationsテーブルと1対多のリレーション構築
     */
    public function userLocation()
    {
        return $this->hasMany('App\Model\UserLocation', 'id', 'user_id');
    }
    /**
     * markersテーブルと多対多のリレーション構築(中間テーブル:user_markers)
     */
    public function marker()
    {
        return $this->belongsToMany(
            'App\Model\Marker',             // 結合先テーブル
            'user_markers',                 // 中間テーブル名
            'user_id',                      // 中間テーブルにあるFK
            'marker_id'                     // リレーション先モデルのFK
        );
    }
    /**
     * community_locationsテーブルと1対1のリレーション構築
     */
    public function communityLocation()
    {
        return $this->hasOne('App\Model\CommunityLocation', 'id', 'user_id');
    }
    /**
     * user_points_historyテーブルと1対多のリレーション構築
     */
    public function user_point_history()
    {
        return $this->hasMany('App\Model\UserPointsHistory', 'id', 'user_id');
    }
    /**
     * newsテーブルと1対多のリレーション構築
     */
    public function news()
    {
        return $this->hasMany('App\Model\News', 'id', 'update_user_id');
    }
}
