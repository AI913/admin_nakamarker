<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class News extends BaseModel
{
    /**
     * 保存対象(attribute)
     * @var array
     */
    protected $fillable = ['type', 'title', 'body', 'image_file', 'condition_start_time', 'condition_end_time', 'status', 'memo', 'del_flg'];

    /**
     * テーブル名を格納(オーバーライドで使用)
     */
    public function setTableName() {
        return $this->table = 'news';
    }

    // usersテーブルと1対多のリレーション構築(1側)
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'update_user_id', 'id');
    }
}
