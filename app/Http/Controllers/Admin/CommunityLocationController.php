<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Lib\Common;
use App\Services\Model\CommunityLocationService;
use App\Services\Model\MarkerService;
use App\Services\Model\CommunityService;
use App\Services\Model\ConfigService;

class CommunityLocationController extends BaseAdminController
{
    protected $mainService;
    protected $markerService;
    protected $communityService;
    protected $community;
    protected $communityLocation;
    protected $location_id;

    /**
     * コミュニティロケーション管理コントローラー
     * Class UserController
     * @package App\Http\Controllers
     */
    public function __construct(CommunityLocationService $mainService, MarkerService $markerService, CommunityService $communityService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/community/community-location";
        $this->mainTitle    = 'コミュニティロケーション管理';

        // MarkerServiceとCommunityServiceをインスタンス化
        $this->markerService = $markerService;
        $this->communityService = $communityService;

        // テーブル名の設定
        $this->table = 'community_locations';

        // コミュニティのID等を取得
        $this->middleware(function($request, $next) {
            // GET送信の場合
            $this->community = $this->communityService->searchOne(['id' => $request->id]);
            $this->location_id = $request->location_id;

            if (!$this->community) {
                // POST送信の場合
                $this->community = $this->communityService->searchOne(['id' => $request->community_id]);
                if(!$this->community) {
                    abort(404);
                }
            }

            return $next($request);
        });
    }

    /**
     * 登録項目除外配列(下記以外に不要な項目があればオーバーライド)
     * @return array
     */
    public function except() {
        return ["_token", "register_mode", "upload_image", "img_delete", "delete_flg_on", "marker_name", 'image_flg', 'map'];
    }

    /**
     * コミュニティロケーションリスト取得
     * @param request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    // public function main_list(Request $request) {
    //     // マーカ名をマーカーのIDに変換
    //     if ($request->marker_name) { 
    //         $marker = $this->markerService->searchOne(['name@like' => $request->marker_name]);
    //     }
    //     echo $this->community->id;
    //     // 〇検索条件
    //     $conditions = [];
    //     if ($request->id) { $conditions['community_locations.id'] = $request->id; }
    //     if ($request->marker_name) { $conditions['community_locations.marker_id'] = $marker->id; }
    //     if ($request->name) { $conditions['community_locations.name@like'] = $request->name; }

    //     // コミュニティの登録場所とそれに紐づくマーカー情報を取得
    //     return DataTables::eloquent($this->mainService->getCommunityLocationQuery($this->community->id, $conditions))->make();
    // }

    /**
     * コミュニティロケーション一覧
     * 
     */
    public function index() {
        return parent::index()->with(
            [
                'community_id'      => $this->community->id,
                'community_name'    => $this->community->name,
            ]
        );
    }

    /**
     * コミュニティロケーションの'備考'データ取得
     * @param request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getMemo($id, $location_id) {
        $data = $this->mainService->getLocationMemoQuery($location_id)->first();

        return [
            'status' => 1,
            'data' => $data,
        ]; 
    }

    /**
     * ロケーション作成機能
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        // 全マーカーデータを取得
        $marker_list = $this->markerService->all();

        // コミュニティ情報
        $community_id = $this->community->id;
        // dd($data);
        // マーカーリスト&コミュニティリスト追加
        return parent::create()->with([
            'register_mode' => 'create',
            'marker_list'   => $marker_list,
            'community_id'  => $community_id,
        ]);
    }

    /**
     * ロケーション編集機能
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {
        // 編集対象のロケーションデータを取得
        $data = $this->mainService->find($this->location_id);

        // 全マーカーデータを取得
        $marker_list = $this->markerService->all();

        // コミュニティIDの代入
        $community_id = $this->community->id;

        // ロケーションデータに紐づいたマーカーを取得
        $marker = $this->markerService->searchOne(['id' => $data->marker_id]);

        // 緯度・経度を連結
        $data['map'] = $data->latitude.', '.$data->longitude;

        // マーカー名とコミュニティ名を$dataに追加
        $data['marker_name'] = $marker->name;

        return view($this->mainRoot.'/register', [
            'register_mode' => 'edit',
            'marker_list'   => $marker_list,
            'data'          => $data,
            'community_id'  => $community_id,
            'folder' => $this->table,
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
        // 選択したマーカーとコミュニティのデータをそれぞれ取得
        $marker = $this->markerService->searchOne(['name' => $request->marker_name]);
        
        // 除外項目
        $input = $request->except($this->except());
        
        // マーカーとコミュニティのIDを配列に追加
        $input['marker_id'] = $marker->id;
        $input['community_id'] = $this->community->id;

        if(is_null($request->image_flg)) {
            // 強制削除フラグがONの場合、専用画像名をDBに保存
            if(empty($request->file('upload_image')) && $request->delete_flg_on === 'true') {
                $input['image_file'] = config('const.out_image');
            }
            
            // 強制削除フラグがOFFでかつ画像がアップロードされていない場合、nullをDBに保存
            if(empty($request->file('upload_image')) && $request->delete_flg_on === 'false') {
                $input['image_file'] = null;
            }
        }

        // 画像名をレコードに設定
        if(\Session::get('file_name')) {
            $input['image_file'] = \Session::get('file_name');
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
        // 緯度・経度を分割
        $map = explode(',', $request->map);

        // 緯度・経度を数値化して配列に追加
        $request['latitude'] = $map[0];
        $request['longitude'] = $map[1];
        
        // 加工したリクエストを追加
        $request = $this->addRequest($request);

        // 削除ボタンを押下して画像を設定しない場合
        if($request['img_delete'] && $request['img_delete'] == 1) {
            // 設定済みだった画像をストレージから削除
            Common::removeImage($request->image_file, $this->table);

            // セッションの値を削除
            \Session::forget('file_path');
            \Session::forget('file_name');
        }

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
            return redirect(route('admin/community/detail/location/index', ['id' => $this->community->id]))->with('info_message', $request->register_mode == 'create' ? $this->mainTitle.'情報を登録しました' : $this->mainTitle.'情報を編集しました');
        } catch (\Exception $e) {
            \DB::rollBack();
            // 保存処理を終えていた画像を削除
            if($input["image_file"]) {
                Common::removeImage($input["image_file"]);
            }
            return redirect(route('admin/community/detail/location/index', ['id' => $this->community->id]))->with('error_message', 'データ登録時にエラーが発生しました。[詳細]<br>'.$e->getMessage());
        }
    }

    /**
     * 削除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function remove(Request $request) {
        $this->mainService->remove($request->id);

        return redirect(route('admin/community/detail/location/index', ['id' => $request->id]))->with('info_message', $this->mainTitle.'情報を削除しました');
    }

    /**
     * バリデーション設定
     * @param Request $request
     * @return array
     */
    public function validation_rules(Request $request)
    {
        // dd($request);
        // バリデーションチェック
        return [
            // 緯度：-90~90の間を正規表現でチェック、経度：-180~180の間を正規表現でチェック
            'latitude'          => ['numeric'],
            'longitude'         => ['numeric'],
            'upload_image'      => ['image', 'max:1024'],
        ];
    }

    /**
     * バリデーションメッセージ
     * @param Request $request
     * @return array
     */
    public function validation_message(Request $request) {
        return [
            'latitude.numeric'     => '緯度は半角数字で入力してください',
            'longitude.numeric'     => '経度は半角数字で入力してください',

            'upload_image.image'  => '画像は"jpeg, png, bmp, gif, or svg"形式のみでアップロードしてください',
            'upload_image.max'    => '画像は1,024kb以下しか登録できません',
        ];
    }

    /**
     * バリデーションエラー時のリダイレクト処理
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function validationFailRedirect(Request $request, $validator) {
        return redirect($this->isCreate($request) ? 'community/detail/'.$this->community->id.'/location/create'
                                                  : 'community/detail/'.$this->community->id.'/location/edit/'.$this->location_id)
            ->withErrors($validator)
            ->withInput();
    }
}
