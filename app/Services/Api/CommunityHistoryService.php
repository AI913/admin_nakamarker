<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\CommunityHistory;

class CommunityHistoryService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(CommunityHistory $model) {
        $this->model = $model;
    }

    /**
     * コミュニティにユーザが所属するか判定
     * 引数：検索条件
     */
    public function isCommunityUser($conditions) {
        // 対象コミュニティの履歴データを取得
        $community_histories = $this->searchList($conditions);

        // ユーザが対象コミュニティに所属しているか確認
        foreach($community_histories as $key => $value) {
            // 所属 + 申請状況が"承認"であることが条件
            if($value->user_id == \Auth::user()->id && $value->status == config('const.community_history_approval')) {
                return true;
            }
        }
        return false;
    }

}