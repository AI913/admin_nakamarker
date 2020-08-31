<?php
namespace App\Services\Model;

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
     * 申請状況の編集処理
     * 引数：
     */
    public function updateStatus($request) {
        $model = $this->searchOne(['id' => $request->id]);

        // 申請状況の値を切り替え
        if($request->status == config('const.community_history_apply')) {
            $model->status = config('const.community_history_approval');
        } elseif ($request->status == config('const.community_history_approval')) {
            $model->status = config('const.community_history_reject');
        } elseif ($request->status == config('const.community_history_reject')) {
            $model->status = config('const.community_history_apply');
        }
        $model->update_user_id  = \Auth::user()->id;
        $model->updated_at      = date('Y-m-d H:i:s');
        $model->save();
        return $model->status;
    }

    /**
     * detailモーダルのデータ取得
     * 引数:ID
     */
    public function get_detail($id) {
        $query = $this->model()->query();

        $query->leftJoin('communities', 'community_histories.community_id', '=', 'communities.id')
              ->select('community_histories.id', 'communities.name', 'communities.status as open_flg')
              ->where('community_histories.id', '=', $id);

        return $query;
    }

    /**
     * コミュニティの参加ユーザ数算出処理
     * 引数:コミュニティID
     */
    public function member_update($community_id) {
        $query = $this->model()->query();

        $query->select('user_id')
              ->where('community_id', '=', $community_id)
              ->where('status', '=', config('const.community_history_approval'));
              
        return $query;
    }
}