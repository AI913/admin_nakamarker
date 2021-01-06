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
        'type_name',
        'charge_name'
    ];

    /**
     * 保存対象(attribute)
     * @var array
     */
    protected $fillable = ['type', 'name', 'description', 'status', 'image_file', 'price', 'charge_flg', 'update_user_id'];

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
        return $this->table = 'markers';
    }

    /**
     * マーカータイプ名を返す
     * @return string
     */
    public function getTypeNameAttribute() {
        if ($this->type == config('const.marker_type_register'))  return config('const.marker_type_register_name');
        if ($this->type == config('const.marker_type_function'))     return config('const.marker_type_function_name');
        if ($this->type == config('const.marker_type_search'))   return config('const.marker_type_search_name');
    }

    /**
     * マーカーの有料フラグを返す
     * @return string
     */
    public function getChargeNameAttribute() {
        if ($this->charge_flg == config('const.charge_flg_off'))  return config('const.charge_flg_off_name');
        if ($this->charge_flg == config('const.charge_flg_on'))     return config('const.charge_flg_on_name');
        if ($this->charge_flg == config('const.charge_flg_default'))     return config('const.charge_flg_default_name');
    }

    /**
     * usersテーブルと多対多のリレーション構築(中間テーブル:user_markers)
     */
    public function user()
    {
        return $this->belongsToMany(
            'App\Model\User',               // 結合先テーブル
            'user_markers',                 // 中間テーブル名
            'marker_id',                    // 中間テーブルにあるFK
            'user_id'                       // リレーション先モデルのFK
        );
    }
    /**
     * user_locationsテーブルと1対1のリレーション構築
     */
    public function userLocation()
    {
        return $this->hasOne('App\Model\UserLocation', 'id', 'marker_id');
    }

    /**
     * communitiesテーブルと多対多のリレーション構築(中間テーブル:community_markers)
     */
    public function community()
    {
        return $this->belongsToMany(
            'App\Model\Community',          // 結合先テーブル
            'community_markers',            // 中間テーブル名
            'marker_id',                    // 中間テーブルにあるFK
            'community_id'                  // リレーション先モデルのFK
        );
    }
    /**
     * community_locationsテーブルと1対1のリレーション構築
     */
    public function communityLocation()
    {
        return $this->hasOne('App\Model\CommunityLocation', 'id', 'marker_id');
    }
}
