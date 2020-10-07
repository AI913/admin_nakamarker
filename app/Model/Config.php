<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Config extends BaseModel
{
    /**
     * ユーザーマスタリレーション(最終更新者用)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function update_user() {
        return $this->belongsTo('App\Model\User', 'update_user_id', 'id')->withDefault([
            'name' => '----'
        ]);
    }
}
