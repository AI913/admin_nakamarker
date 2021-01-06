<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\CommunityLocation;

class CommunityLocationService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(CommunityLocation $model) {
        $this->model = $model;
    }

    /**
     * コミュニティマーカーで登録されたデータを取得
     * 引数1: コミュニティID, 引数2: ロケーションID(一覧を表示する場合は要省略), 引数3：ソート条件
     */
    public function getCommunityLocationQuery($conditions=[], $order=[]) {
        // 削除フラグ排除のため、searchQuery()を実行
        $query = $this->searchQuery($conditions)
                      ->select('id as location_id', 'name as location_name', 'latitude', 'longitude', 
                               'image_file', 'memo', 'marker_id', 'user_id', 'created_at')
                      ->with('marker:id,name as marker_name,type as marker_type', 
                             'user:id,name as user_name')
                      ->get();

        // ソート条件
        foreach($order as $key => $value) {
            switch ($key) {
                // 作成日時の昇順
                case 99:
                    $query = $query->sortBy('created_at');
                break;
                // 作成日時の降順
                case -99:
                    $query = $query->sortByDesc('created_at');
                break;
                // ロケーション名で昇順
                case 1:
                    $query = $query->sortBy('location_name');
                break;
                // ロケーション名で降順
                case -1:
                    $query = $query->sortByDesc('location_name');
                break;
                // マーカーの種別で昇順
                case 2:
                    $query = $query->sortBy('marker.marker_type');
                break;
                // マーカーの種別で降順
                case -2:
                    $query = $query->sortByDesc('marker.marker_type');
                break;
                // マーカーの名前で昇順
                case 3:
                    $query = $query->sortBy('marker.marker_name');
                break;
                // マーカーの名前で降順
                case -3:
                    $query = $query->sortByDesc('marker.marker_name');
                break;
                // ユーザの名前で昇順
                case 4:
                    $query = $query->sortBy('user.user_name');
                break;
                // ユーザの名前で降順
                case -4:
                    $query = $query->sortByDesc('user.user_name');
                break;
            }
        }

        return $query;
    }
    
    /**
     * ロケーション登録の本人確認
     * 引数1: ロケーションID, 引数2: ユーザID
     */
    public function isRegisterUser($location_id, $user_id) {
        // 対象ロケーションのユーザIDとログインユーザのIDが一致するか確認
        return $this->searchExists(['id' => $location_id, 'user_id' => $user_id]);
    }
}