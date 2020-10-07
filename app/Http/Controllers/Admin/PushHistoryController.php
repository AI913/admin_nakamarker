<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\PushHistoryService;
use App\Lib\Common;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Validation\Rule;

class PushHistoryController extends BaseAdminController
{
    /**
     * 通知機能管理コントローラー
     * Class PushHistoryController
     * @package App\Http\Controllers
     */
    public function __construct(PushHistoryService $mainService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/push-history";
        $this->mainTitle    = 'プッシュ通知履歴管理';
    }

    /**
     * メインリストデータ取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function main_list(Request $request) {
        // 〇検索条件
        $conditions = [];
        $conditions['del_flg'] = 0;
        if ($request->id) { $conditions['push_histories.id'] = $request->id; }
        if ($request->title) { $conditions['push_histories.title@like'] = $request->title; }
        if ($request->type) { $conditions['push_histories.type'] = $request->type; }
        // statusのリクエストがあり、かつリクエストが数値の場合に検索条件の値をセットする
        if ($request->has('status') && is_numeric($request->status)) { $conditions['push_histories.status'] = $request->status; }
        
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = [];
        return DataTables::eloquent($this->mainService->searchQuery($conditions, $sort, $relations))->make();
    }


    /**
     * プッシュ通知管理一覧
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // ステータスリスト追加
        return parent::index()->with([
            'type_list' => Common::getPushTypeList(),
            'status_list' => Common::getPushStatusList(),
        ]);
    }

    /**
     * モーダルに必要なデータを取得
     * @param $user_id
     * @return array
     */
    public function detail($id) {
        
        // 詳細(Modal)のDataTable
        // 〇検索条件
        $conditions = [];
        $conditions['id'] = $id;
        $conditions['del_flg'] = 0;
        // 〇ソート条件
        $sort = [];
        // 〇リレーション
        $relations = [];
        $data = $this->mainService->searchOne($conditions, $sort, $relations);

        return [
            'status' => 1,
            'data' => $data,
        ]; 
    }

    /**
     * プッシュ通知送信機能
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function send() {
        // ステータスリスト追加
        return parent::index()->with([
            'type_list' => Common::getPushTypeList(),
            'status_list' => Common::getPushStatusList(),
        ]);
    }

    /**
     * バリデーションエラー時のリダイレクト処理
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function validationFailRedirect(Request $request, $validator) {
        return redirect($this->isCreate($request) ? route("admin/push/create") :  route("admin/push/edit", ['id' => $request->id]))
            ->withErrors($validator)
            ->withInput();
    }

    /**
     * バリデーション設定
     * @param Request $request
     * @return array
     */
    public function validation_rules(Request $request)
    {
        // バリデーションチェック
        return [
            'reservation_date'  => ['after:"now"'],
        ];
    }

    /**
     * バリデーションメッセージ
     * @param Request $request
     * @return array
     */
    public function validation_message(Request $request) {
        return [
            'reservation_date.after'        => '送信予約日時は現在以前の日時を指定できません',
        ];
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Exception
     */
    public function save(Request $request) {

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
            return redirect(route('admin/push'))->with('info_message', $request->register_mode == 'create' ? $this->mainTitle.'情報を登録しました' : $this->mainTitle.'情報を編集しました');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect(route('admin/push'))->with('error_message', 'データ登録時にエラーが発生しました。[詳細]<br>'.$e->getMessage());
        }
    }

    /**
     * 削除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function remove(Request $request) {
        $this->mainService->remove($request->id);
        return redirect(route("admin/push"))->with('info_message', $this->mainTitle.'情報を削除しました');
    }
}
