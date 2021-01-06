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
    protected $child_appends = ['start_time', 'end_time'];

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
     * テーブル名を格納(オーバーライドで使用)
     */
    public function setTableName() {
        return $this->table = 'news';
    }

    /**
     * usersテーブルと1対多のリレーション構築(1側)
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'update_user_id', 'id');
    }

    /**
     * 時刻表記(condition_start_time)
     * @return string
     */
    public function getStartTimeAttribute() {
        if(!is_null($this->condition_start_time)) {
            return date("Y年m月d日 H時i分" , strtotime($this->condition_start_time));
        }
    }
    /**
     * 時刻表記(condition_end_time)
     * @return string
     */
    public function getEndTimeAttribute() {
        if(!is_null($this->condition_end_time)) {
            return date("Y年m月d日 H時i分" , strtotime($this->condition_end_time));
        }
    }
}
