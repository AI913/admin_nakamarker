<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CommunityHistory extends BaseModel
{
    /**
     * 申請状況を返す
     * (数値からconst.phpで定義した文字列へと変換)
     * @return string
     */
    public function getStatusNameAttribute() {
        if ($this->status == config('const.community_history_apply'))  return config('const.community_history_apply_name');
        if ($this->status == config('const.community_history_approval'))     return config('const.community_history_approval_name');
        if ($this->status == config('const.community_history_reject'))   return config('const.community_history_reject_name');
    }

    // usersテーブルと1対多のリレーション構築(多側)
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'id');
    }
    // communitiesテーブルと1対多のリレーション構築(多側)
    public function community() {
        return $this->belongsTo('App\Model\Community', 'community_id', 'id');
    }
}
