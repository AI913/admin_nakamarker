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

    
    /**
     * ユーザ一覧ページに表示するコミュニティデータを取得
     * 引数:ユーザID
     */
    public function isUserCommunityData($user_id) {
        $query = $this->model()->query();
    
        $query->leftJoin('community_histories', 'communities.id', 'community_histories.community_id')
              ->leftJoin('users', 'community_histories.user_id', 'users.id')
              ->select('communities.*', 'users.id as user_id', 'community_histories.status as entry_status')
              ->where('community_histories.user_id', '=', $user_id);
              
        return $query;
    }

    /**
     * コミュニティの参加ユーザ数保存処理
     * 引数:コミュニティの参加人数
     */
    public function member_save($count_member) {
        $query = $this->model()->query();

        $query->member = $count_member;

        return $query->save();
    }
}