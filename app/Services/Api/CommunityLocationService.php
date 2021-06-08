<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\CommunityLocation;
use App\Services\Api\MarkerService;
use App\Services\Api\UserService;

class CommunityLocationService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(CommunityLocation $model, UserService $userService, MarkerService $markerService) {
        $this->model = $model;

        $this->userService = $userService;
        $this->markerService = $markerService;
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
      * 全コミュニティの場所更新情報取得
      * @param array $conditions(検索条件)
      *      (例)：　$conditions[] = ['key' => $value]
      *             key は条件によって、オプション付与可能(カラム@条件)
      *              (例)： 'name@like' @likeを付与し、like検索が可能
      * @param array $order(ソート条件)
      *      (例)：　$order[] = ['key' => 'desc' or 'asc']
      * @param int $offset(取得開始するindex)
      * @return \Illuminate\Database\Eloquent\Builder|mixed
      */
    public function getCommunityLocationUpadateQuery($conditions=[], $order=[], $offset=0) {
      $relations = ['user' => [], 'community' => []];

      $extract = $this->searchQuery($conditions, $order, $relations, 100, $offset)->get();
      $returnData = [];
      foreach ($extract as $value) {
        $action = "";
        if ($value['del_flg'] == 1){
          $action = "削除";
        } else {
          if ($value['created_at'] == $value['updated_at']){
            $action = "登録";
          } else {
            $action = "編集";
          }
        }

        $tmp['community_id'] = $value['community_id'];
        $tmp['image_url'] = $value['image_url'];
        $tmp['updated_at'] = $value['updated_at'];
        $tmp['text'] = $value['community']['name']." の ".
                       $this->userService->find($value['update_user_id'])->name." が ".
                       $this->markerService->find($value['marker_id'])->name." を ".
                       $action." しました";

        array_push($returnData, $tmp);
      }

      return $returnData;
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
