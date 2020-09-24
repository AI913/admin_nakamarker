<?php

namespace App\Model;

use App\Lib\Common;
use Illuminate\Database\Eloquent\Model;

/**
 * BaseModel
 * Class BaseModel
 * @package App
 */
class BaseModel extends Model {

    // 主キーID
    protected $guarded = ['id'];

    protected $dates = ['created_at', 'updated_at'];
    
    // テーブル名
    protected $table;

    /**
     * 独自アクセサ(attribute)
     * @var array
     */
    protected $appends = ['image_url', 'memo_html', 'status_name', 'created_at_style', 'updated_at_style'];

    /**
     * テーブル名を格納(オーバーライドで使用)
     */
    public function setTableName() {
        return $this->table;
    }

    /**
     * 画像URL取得
     * @return string
     */
    public function getImageUrlAttribute() {
        return Common::getImageUrl($this->image_file, $this->table);
    }

    /**
     * 公開ステータス名を返す
     * @return string
     */
    public function getStatusNameAttribute() {
        if ($this->status == config('const.open'))  return config('const.open_name');
        if ($this->status == config('const.private'))     return config('const.private_name');
    }
    /**
     * 備考
     * @return string
     */
    public function getMemoHtmlAttribute() {
        return nl2br($this->memo);
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
}
