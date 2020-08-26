<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\Community;

class CommunityService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(Community $model) {
        $this->model = $model;
    }

    // ユーザ一覧ページに表示するコミュニティデータを取得
    public function isUserCommunityData($user_id) {
        $query = $this->model()->query();
    
        $query->leftJoin('community_histories', 'communities.id', 'community_histories.community_id')
              ->leftJoin('users', 'community_histories.user_id', 'users.id')
              ->select('communities.*')
              ->where('community_histories.user_id', '=', $user_id)
              ->where('community_histories.status', '=', config('const.community_history_approval'));
              
        return $query;
    }
}