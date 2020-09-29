<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\Common;
use App\Services\Model\ConfigService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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

    // テーブル名の設定(画像の保存時に使用)
    protected $table;


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
     * バリデーションルール　※オーバーライドして使用する
     * @param Request $request
     * @return array
     */
    public function validation_rules(Request $request) {
        return [];
    }

    /**
     * バリデーションメッセージ　※オーバーライドして使用する
     * @param Request $request
     * @return array
     */
    public function validation_message(Request $request) {
        return [];
    }

    /**
     * 一覧画面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // 新規作成をキャンセルした場合の画像削除処理
        if(session('file_name')) {
            if(!$this->mainService->searchExists(['image_file' => session('file_name')])) {
                Common::removeImage(session('file_name'), $this->table);
            }
        }
        // 新規作成をキャンセルした場合のセッションの削除
        if(session('file_path')) {
            session()->forget('file_path');
        }
        if(session('file_name')) {
            session()->forget('file_name');
        }
        
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
            'data' => $this->mainService->find($id),
            'folder' => $this->table,
        ]);
    }

    /**
     * 新規登録かどうか
     * @param Request $request
     * @return bool
     */
    public function isCreate(Request $request) {
        return isset($request->register_mode) && $request->register_mode == "create";
    }
    /**
     * 編集かどうか
     * @param Request $request
     * @return bool
     */
    public function isEdit(Request $request) {
        return isset($request->register_mode) && $request->register_mode == "edit";
    }

    /**
     * 保存前に、加工したリクエストを追加
     * @param Request $request
     * @return Request
     */
    public function addRequest(Request $request) {
        return $request;
    }

    /**
     * バリデーション
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function validation(Request $request) {
        // リクエスト変数を追加
        $request = $this->addRequest($request);

        // バリデーションルール
        return Validator::make($request->all(), $this->validation_rules($request), $this->validation_message($request));
    }

    /**
     * バリデーションエラー時のリダイレクト処理
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function validationFailRedirect(Request $request, $validator) {
        return redirect($this->isCreate($request) ? route($this->mainRoot."/create") :  route($this->mainRoot."/edit", ['id' => $request->id]))
            ->withErrors($validator)
            ->withInput();
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

        // if(is_null($request->image_flg)) {
        if($request->img_delete == 1) {
            // 強制削除フラグがOFFでかつ画像がアップロードされていない場合、nullをDBに保存
            if(is_null($request->file('upload_image'))) {
                $input['image_file'] = null;
            }

            // 強制削除フラグがONの場合、専用画像名をDBに保存
            if(is_null($request->file('upload_image')) && $request->delete_flg_on === 'true') {
                // dd($request);
                $input['image_file'] = config('const.out_image');
            }
        }

        // 画像名をレコードに設定
        if(\Session::get('file_name')) {
            $input['image_file'] = \Session::get('file_name');
        }

        return $input;
    }

    /**
     * ファイル保存処理
     */
    public function fileSave(Request $request) {
        // アップロードしたファイルが画像かどうかを判別
        $rules = [
            'upload_image'  => ['image', 'max:1024'],
        ];
        $validator = Validator::make($request->all(), $rules, $this->validation_message($request));

        // バリデーションエラー時はリダイレクト
        // ※画像のみ要件を満たしていれば後続の処理へと続く
        if ($validator->fails()) {
            return $this->validationFailRedirect($request, $validator);
        }

        // 画像の新規保存
        $filename = Common::saveImage($request->file('upload_image'), $this->table);
        // 画像のパスと名前をセッションに保存
        \Session::put('file_path', Storage::url("images/".$this->table.'/'.$filename));
        \Session::put('file_name', $filename);

        return;
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Exception
     */
    public function save(Request $request) {
        // 画像ありの場合は保存処理実行
        if ($request->hasFile('upload_image')) {
            $this->fileSave($request);
        }

        // バリデーション
        $validator = $this->validation($request);
        // バリデーションエラー時はリダイレクト
        if ($validator->fails()) {

            return $this->validationFailRedirect($request, $validator);
        }
        
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
            // 先に保存処理を終えていた画像を削除(ロールバック処理)
            if(isset($input["image_file"])) {
                Common::removeImage($input["image_file"], $this->table);
            }
            return redirect(route($this->mainRoot))->with('error_message', 'データ登録時にエラーが発生しました。[詳細]<br>'.$e->getMessage());
        }
    }

    /**
     * 保存後処理
     * @param Request $request
     * @param $model
     */
    public function saveAfter(Request $request, Model $model) {
        // 削除ボタンを押下して画像を設定しない状態でデータを保存する場合
        if($request['img_delete'] && $request['img_delete'] == 1) {
            // 設定済みだった画像をストレージから削除
            if(session('file_name')) {
                Common::removeImage(session('file_name'), $this->table);
            } else {
                Common::removeImage($request->image_file, $this->table);
            }

            // セッションの値を削除
            \Session::forget('file_path');
            \Session::forget('file_name');
        }

        // 編集の場合、新たに登録する画像があった場合は登録済みの画像を削除
        if ($request->register_mode == "edit" && session('file_name')) {
            Common::removeImage($request->image_file, $this->table);
        }
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
