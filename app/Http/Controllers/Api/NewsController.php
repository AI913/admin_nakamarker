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
            // ソート条件
            $order = [];
            if (isset($request->order)) {
              $order = [$request->order[0] => $request->order[1]];
            }
            // 取得件数の設定(configsテーブルのnews_listというkeyカラムで件数を設定する)
            $limit = 0;
            $config = $this->configService->searchOne(['key' => 'news_list']);
            $config->value ? $limit = $config->value : '';
            
            // ニュース一覧データを取得
            $news = $this->mainService->getNewsQuery($order, $limit)->get();

            // ステータスOK
            return $this->success(['news' => $news]);

        } catch (\Exception $e) {
            return $this->error(-9, ["message" => __FUNCTION__.":".$e->getMessage()]);
        }
    }
}
