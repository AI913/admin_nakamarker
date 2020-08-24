<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\Common;
use App\Services\Model\ConfigService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 * 管理画面用Baseコントローラー
 * Class BaseAdminController
 * @package App\Http\Controllers\Admin
 */
class BaseAdminController extends Controller
{
    // メインサービス
    protected $mainService;
    // メインルート
    protected $mainRoot;
    // メインタイトル
    protected $mainTitle;
    // メインリスト
    protected $mainList;
    
    // システム設定サービス
    protected $configService;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        // 子クラスは必ず、サービス・ルート・タイトルを子クラスで定義する
        $this->middleware(function ($request, $next) {

            // 共通設定サービス
            // View::share('config_service', $this->configService);
            
            return $next($request);
        });
    }

    /**
     * 登録項目除外配列(下記以外に不要な項目があればオーバーライド)
     * @return array
     */
    public function except() {
        return ["_token", "register_mode", "upload_image"];
    }

    /**
     * 一覧画面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        return view($this->mainRoot.'/list');
    }

    /**
     * 詳細(Modal)
     * @return array
     */
    public function detail($id) {
       return view($this->mainRoot.'/detail', [
           'data' => $this->mainService->find($id)
       ]);
    }

    /**
     * 新規登録
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        return view($this->mainRoot.'/register', [
            'register_mode' => 'create',
            'data' => $this->mainService->model()
        ]);
    }

    /**
     * 編集
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {
        return view($this->mainRoot.'/register', [
            'register_mode' => 'edit',
            'data' => $this->mainService->find($id)
        ]);
    }

    /**
     * 保存前処理
     * @param Request $request
     * @return array
     * @throws \Exception
     * $request->image_file : inputタイプのhidden属性
     * $request->file('upload_image') : inputタイプのfile属性
     */
    public function saveBefore(Request $request) {
        // 保存処理モード
        $register_mode = $request->register_mode;
        // 除外項目
        $input = $request->except($this->except());
        // 画像あり
        if ($request->hasFile('upload_image')) {
            // 編集の場合、登録済みの画像削除
            if ($register_mode == "edit") {
                Common::removeImage($request->image_file);
            }
            // 画像の新規保存
            $input["image_file"] = Common::saveImage($request->file('upload_image'));
        }
        return $input;
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Exception
     */
    public function save(Request $request) {
        try {
            \DB::beginTransaction();
            // 保存前処理で保存データ作成
            $input = $this->saveBefore($request);
            // 保存処理
            $model = $this->mainService->save($input, true, false);
            // 保存後処理
            $this->saveAfter($request, $model);
            \DB::commit();
            // 対象データの一覧にリダイレクト
            return redirect(route($this->mainRoot))->with('info_message', $request->register_mode == 'create' ? $this->mainTitle.'情報を登録しました' : $this->mainTitle.'情報を編集しました');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect(route($this->mainRoot))->with('error_message', 'データ登録時にエラーが発生しました。[詳細]<br>'.$e->getMessage());
        }
    }

    /**
     * 保存後処理
     * @param Request $request
     * @param $model
     */
    public function saveAfter(Request $request, Model $model) {
        return;
    }

    /**
     * 削除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function remove(Request $request) {
        $this->mainService->remove($request->id);
        return redirect(route($this->mainRoot))->with('info_message', $this->mainTitle.'情報を削除しました');
    }
}
