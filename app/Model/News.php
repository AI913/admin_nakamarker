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
     * 独自アクセサ(attribute)
     * @var array
     */
    protected $child_appends = [
        'type_name'
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
     * 種別名を返す
     * (数値からconst.phpで定義した文字列へと変換)
     * @return string
     */
    public function getTypeNameAttribute() {
        if ($this->type == config('const.official_type'))      return config('const.official_type_name');
        if ($this->type == config('const.community_type'))     return config('const.community_type_name');
    }

    // usersテーブルと1対多のリレーション構築(1側)
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'update_user_id', 'id');
    }
}
