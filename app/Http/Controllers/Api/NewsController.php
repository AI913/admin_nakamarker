<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\NewsService;
use App\Services\Api\ConfigService;

class NewsController extends BaseApiController
{
    protected $mainService;
    protected $configService;

    /**
     * ニュース管理コントローラー
     * Class NewsController
     * @package App\Http\Controllers
     */
    public function __construct(NewsService $mainService, ConfigService $configService) {
        $this->mainService  = $mainService;
        $this->configService = $configService;
    }

    /**
     * ニュース一覧情報取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        try {
            $order = $this->getSortOrder($request);
            $limit = $this->configService->searchOne(['key' => 'news_list'])->value;

            $returnData = [];
            foreach ($this->mainService->getNewsQuery($order, $limit, $request->offset) as $data) {
              array_push($returnData, [
                'id'   => $data['id'],
                'title' => $data['title'],
                'body' => $data['body'],
                'image_url' => $data['image_url'],
                'condition_start_time' => $data['condition_start_time'],
                'condition_end_time' => $data['condition_end_time']
              ]);
            }

            return $this->success(['news_list' => $returnData]);
        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
}
