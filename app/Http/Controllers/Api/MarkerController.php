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
    public function index(Request $request) {
        try {
            // ソート条件
            $order = [];
            if(key_exists('order', $request->all())) {
                $sort = $request->input('order'); 
                $order[$sort] = $sort;
            }

            // マーカー一覧データを取得
            $markers = $this->mainService->getMarkerQuery($order)->get();
            // ステータスOK
            return $this->success(['markers' => $markers]);

        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
}
