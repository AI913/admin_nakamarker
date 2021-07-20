<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\MarkerService;

class MarkerController extends BaseApiController
{
    protected $mainService;

    /**
     * マーカー管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(
        MarkerService $mainService
    ) {
        $this->mainService  = $mainService;
    }

    /**
     * マーカー一覧情報取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllMarker(Request $request)
    {
        try {
            // 検索条件
            $conditions = [];
            $conditions['status'] = config('const.open');  // 公開フラグの値が"公開"のみに限定
            if ($request->input('name')) {
                $conditions['name@like'] = $request->input('name');
            }
            if ($request->input('type')) {
                $conditions['type'] = $request->input('type');
            }
            if ($request->input('charge_type')) {
                $conditions['charge_type'] = $request->input('charge_type');
            }
            
            $order = $this->getSortOrder($request);
            $returnData = [];
            foreach ($this->mainService->getMarkerQuery($conditions, $order) as $data) {
              array_push($returnData, [
                'marker_id'   => $data['id'],
                'type' => $data['type'],
                'name' => $data['name'],
                'search_word' => $data['search_word'],
                'description' => $data['description'],
                'price' => $data['price'],
                'charge_type' => $data['charge_type'],
                'status' => $data['status'],
                'image_url' => $data['image_url']
              ]);
            }

            return $this->success(['markers' => $returnData]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__ . ":" . $e->getMessage()]);
        }
    }
}
