<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Marker extends BaseModel
{
    /**
     * 独自アクセサ(attribute)
     * @var array
     */
    protected $child_appends = [
        'type_name'
    ];

    /**
     * 保存対象(attribute)
     * @var array
     */
    protected $fillable = ['type', 'name', 'description', 'status', 'image_file'];

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
     * マーカータイプ名を返す
     * @return string
     */
    public function getTypeNameAttribute() {
        if ($this->type == config('const.marker_type_register'))  return config('const.marker_type_register_name');
        if ($this->type == config('const.marker_type_function'))     return config('const.marker_type_function_name');
        if ($this->type == config('const.marker_type_search'))   return config('const.marker_type_search_name');
        if ($this->type == config('const.marker_type_community'))   return config('const.marker_type_community_name');
    }

    // user_locationsテーブルと1対1のリレーション構築
    public function userLocation()
    {
        return $this->hasOne('App\Model\UserLocation', 'id', 'marker_id');
    }
    // community_locationsテーブルと1対1のリレーション構築
    public function communityLocation()
    {
        return $this->hasOne('App\Model\CommunityLocation', 'id', 'marker_id');
    }
}
