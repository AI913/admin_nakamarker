<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Lib\Common;
use App\Services\Model\ConfigService;

class BaseApiController extends Controller
{
    protected $configService;
    // テーブル名の設定(画像の保存時に使用)
    protected $table;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // 本番ではクエリログを保持しない。※メモリを食うため
        if (!env('APP_DEBUG')) {
            \DB::connection()->disableQueryLog();
        }
        $this->configService = new ConfigService();
    }

    /**
     * 登録項目除外配列(下記以外に不要な項目があればオーバーライド)
     * @return array
     */
    public function except() {
        return ["_token", "register_mode", "upload_image"];
    }

    /**
     * 正常時レスポンス
     * @param array $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($response = []) {
        $rtn['status'] = 1;
        foreach($response as $key => $value) {
            $rtn[$key] = $value;
        }
        return response()->json($rtn);
    }

    /**
     * エラー時レスポンス
     * @param $status
     * @param array $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($status, $response = []) {
        $rtn['status'] = $status;
        foreach($response as $key => $value) {
            $rtn[$key] = $value;
        }
        \Log::error('status:'.$status.", message:".json_encode($response));
        return response()->json($rtn);
    }

    /**
     * ファイル保存処理
     */
    public function fileSave(Request $request) {

        // 画像の新規保存
        $filename = Common::saveImage($request->file('image'), $this->table);
        
        return $filename;
    }
}
