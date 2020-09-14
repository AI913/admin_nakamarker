<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Community extends BaseModel
{
    /**
     * 独自アクセサ(attribute)
     * @var array
     */
    protected $child_appends = ['entry_status_name', 'type_name'];
    
    /**
     * 保存対象(attribute)
     * @var array
     */
    protected $fillable = ['type', 'name', 'description', 'member', 'status', 'image_file', 'del_flg'];

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
     * コミュニティの種別を返す
     * (数値からconst.phpで定義した文字列へと変換)
     * @return string
     */
    public function getTypeNameAttribute() {
        if ($this->type == config('const.community_official'))  return config('const.community_official_name');
        if ($this->type == config('const.community_official_free'))     return config('const.community_official_free_name');
        if ($this->type == config('const.community_personal'))   return config('const.community_personal_name');
        if ($this->type == config('const.community_personal_open'))   return config('const.community_personal_open_name');
    }

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
