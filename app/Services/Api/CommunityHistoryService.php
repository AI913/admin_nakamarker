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
     * 引数：コミュニティID
     */
    public function isCommunityUser($community_id) {
        // 検索条件の設定
        $conditions = [];
        $conditions['user_id'] = \Auth::user()->id;
        $conditions['status'] = config('const.community_history_approval');
        $conditions['community_id'] = $community_id;
        
        // ユーザの所属有無を判定
        return $this->searchExists($conditions);
    }

}