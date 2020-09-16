<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\UserMarker;

class UserMarkerService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(UserMarker $model) {
        $this->model = $model;
    }

    /**
     * マーカー削除時の履歴削除処理
     * 
     */
    public function cascade($marker_id) {
        \DB::beginTransaction();
        try {
            $model = $this->model()::query();
            $model = $model->where('marker_id', '=', $marker_id)->get();
            
            foreach($model as $value){
                $value->del_flg = 1;
                $value->save();
            }

            \DB::commit();
            return;
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('database remove error:'.$e->getMessage());
            throw new \Exception($e);
        }
        
    }
}