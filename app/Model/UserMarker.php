<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserMarker extends Model
{
    /**
     * 独自アクセサ(attribute)
     * @var array
     */
    protected $child_appends = ['purchace'];

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
     * 時刻表記(updated_at)
     * @return string
     */
    public function getPurchaceAttribute() {
        return date("Y年m月d日 H時i分", strtotime($this->updated_at));
    }
}
