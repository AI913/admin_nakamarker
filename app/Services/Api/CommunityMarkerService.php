<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\CommunityMarker;

class CommunityMarkerService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(CommunityMarker $model) {
        $this->model = $model;
    }

    /**
     * コミュニティマーカーの重複有無を確認
     * 引数1：コミュニティID, 引数2：マーカーID
     */
    public function isDuplicateMarker($community_id, $marker_id) {

        // マーカーの重複チェック(重複していればtrue)
        return $this->searchExists(['community_id' => $community_id, 'marker_id' => $marker_id]);

    }
}
