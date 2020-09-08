
{{--
    ユーザの詳細 & 登録場所のモーダル
        modalのIDは「detail_modal」で固定
        表示したい項目のIDは「detail_xxxx」とdetail_を必ずつける
        また、classには「detail-view」を必ずつける
--}}
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-success modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ユーザ詳細データ</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <ul class="list-inline font-weight-bold">
                    <li class="list-inline-item">ユーザ名</li>
                    <li class="list-inline-item"><span id="detail_name" class="detail-view"></span> さん</li>
                    <li class="list-inline-item">（<span id="detail_status" class="detail-view"></span>）</li>
                </ul>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="item1-tab" data-toggle="tab" href="#item1" role="tab" aria-controls="item1" aria-selected="true">詳細データ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="item2-tab" data-toggle="tab" href="#item2" role="tab" aria-controls="item2" aria-selected="false">登録場所</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="item3-tab" data-toggle="tab" href="#item3" role="tab" aria-controls="item3" aria-selected="false">マーカー</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="item4-tab" data-toggle="tab" href="#item4" role="tab" aria-controls="item4" aria-selected="false">ポイント履歴</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- タブ1つ目(ユーザー詳細)-->
                    <div class="tab-pane fade show active" id="item1" role="tabpanel" aria-labelledby="item1-tab">
                        <div class="row">
                            <div class="col-6">
                                <dl class="row">
                                    <dt class="col-4 text-right">メールアドレス</dt>
                                    <dd class="col-8"><span id="detail_email" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">登録日時</dt>
                                    <dd class="col-8"><span id="detail_created_at" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">最終ログイン日時</dt>
                                    <dd class="col-8"><span id="detail_login_time" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">ユーザエージェント</dt>
                                    <dd class="col-8"><span id="detail_user_agent" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">備考</dt>
                                    <dd class="col-8"><span id="detail_memo" class="detail-view"></span></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    
                    {{-- タブ2つ目(登録場所管理) --}}
                    <div class="tab-pane fade" id="item2" role="tabpanel" aria-labelledby="item2-tab">
                        <table class="table table-striped table-bordered datatable table-sm" id="user_location_list">
                            <thead>
                                <tr role="row">
                                    <th>ロケーションID</th>
                                    <th>マーカー名</th>
                                    <th>場所の名前</th>
                                    <th>ロケーション画像</th>
                                    <th>登録日時</th>
                                    <th>位置情報</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    {{-- 登録場所の備考モーダル --}}
                    <div class="modal fade" id="user_location_modal" tabindex="-1"
                        role="dialog" aria-labelledby="label1" aria-hidden="true">
                        <div class="modal-dialog modal-success modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="label1">備考</h5>
                                    <button type="button" class="close" id="location_modal_close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="container">
                                        <div class="row">
                                            <dt class="col-2 text-right">内容</dt>
                                        </div>
                                        <div class="row">
                                            <dd class="offset-2 col-8"><span id="detail_location_memo" class="detail-view"></span></dd>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="location_modal_close">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- タブ3つ目(マーカー管理) --}}
                    <div class="tab-pane fade" id="item3" role="tabpanel" aria-labelledby="item3-tab">
                        <table class="table table-striped table-bordered datatable table-sm" id="user_markers_list">
                            <thead>
                                <tr role="row">
                                    <th>履歴ID</th>
                                    <th>マーカー画像</th>
                                    <th>マーカー名</th>
                                    <th>価格(ポイント)</th>
                                    <th>有料フラグ</th>
                                    <th>購入日時</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    {{-- タブ4つ目(ポイント履歴管理) --}}
                    <div class="tab-pane fade" id="item4" role="tabpanel" aria-labelledby="item4-tab">
                        <table class="table table-striped table-bordered datatable table-sm" id="user_points_list">
                            <thead>
                                <tr role="row">
                                    <th>履歴ID</th>
                                    <th>付与種別</th>
                                    <th>付与ポイント</th>
                                    <th>消費ポイント</th>
                                    <th>イベント発生日時</th>
                                    <th>有料フラグ</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="user-point-area">
                            <hr>
                            <div class="row">
                                <div class="col-3">
                                    <label class="edit_point_label">ポイントの付与（新規）</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <input type="text" class="form-control" name="give_point" value="" placeholder="付与ポイント"  id="create_point">
                                    <input type="hidden" name="id" value="">
                                </div>
                                <div class="col-2">
                                    <select id="select_point_type" class="form-control">
                                        <option value="" disabled selected>{{ __('付与種別') }}</option>
                                        <option value="1">{{ config('const.point_buy_name') }}</option>
                                        <option value="2">{{ config('const.point_gift_name') }}</option>
                                        <option value="3">{{ config('const.point_advertise_name') }}</option>
                                        <option value="4">{{ config('const.point_admin_name') }}</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select id="select_charge_flg" class="form-control">
                                        <option value="" disabled selected>{{ __('有料フラグ') }}</option>
                                        <option value="1">{{ config('const.charge_flg_off_name') }}</option>
                                        <option value="2">{{ config('const.charge_flg_on_name') }}</option>
                                    </select>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-primary" id="detail_point_submit">付与</button>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-secondary" id="detail_point_reset" data-id="">リセット</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="detail_user_id" value="" />
                <button class="btn btn-secondary" type="button" data-dismiss="modal">閉じる</button>
            </div>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>

{{-- 参加コミュニティのモーダル --}}
<div class="modal fade" id="community_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-warning modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">参加コミュニティ一覧</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <ul class="list-inline font-weight-bold">
                    <li class="list-inline-item">ユーザ名</li>
                    <li class="list-inline-item"><span id="detail_name" class="detail-view"></span> さん</li>
                    <li class="list-inline-item">（<span id="detail_status" class="detail-view"></span>）</li>
                </ul>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" id="item1-tab" data-toggle="tab" href="#item1" role="tab" aria-controls="item1" aria-selected="false">参加コミュニティ一覧</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="item1" role="tabpanel" aria-labelledby="item1-tab">
                        <table class="table table-striped table-bordered datatable table-sm" id="user_community_list">
                            <thead>
                                <tr role="row">
                                    <th>ID</th>
                                    <th>登録画像</th>
                                    <th>コミュニティ名</th>
                                    <th>参加数</th>
                                    <th>公開設定</th>
                                    <th>申請状況の更新日時</th>
                                    <th>申請状況</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                        </table>
                    {{-- </div> --}}
                </div>

                {{-- 登録場所の詳細モーダル --}}
                <div class="modal fade" id="community_history_modal" tabindex="-1" role="dialog" aria-labelledby="label1" aria-hidden="true">
                    <div class="modal-dialog modal-warning modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="label1">コミュニティ履歴の詳細情報</h5>
                                <button type="button" class="close" id="history_modal_close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <div class="row">
                                        <dt class="col-4 text-right">履歴ID</dt>
                                        <dd class="col-8"><span id="detail_history_id" class="detail-view"></span></dd>
                                    </div>
                                    <div class="row">
                                        <dt class="col-4 text-right">コミュニティ名</dt>
                                        <dd class="col-8"><span id="detail_history_name" class="detail-view"></span></dd>
                                    </div>
                                    <div class="row">
                                        <dt class="col-4 text-right">申請状況</dt>
                                        <dd class="col-8"><span id="detail_history_status" class="detail-view"></span></dd>
                                    </div>
                                    <div class="row">
                                        <dt class="col-4 text-right">更新日時</dt>
                                        <dd class="col-8"><span id="detail_history_updated_at" class="detail-view"></span></dd>
                                    </div>
                                    <div class="row">
                                        <dt class="col-4 text-right">備考</dt>
                                        <dd class="col-8"><span id="detail_history_memo" class="detail-view"></span></dd>
                                    </div>    
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="history_modal_close">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="detail_user_id" value="" />
                <button class="btn btn-secondary" type="button" data-dismiss="modal">閉じる</button>
            </div>
        </div>
        <div>
            {{-- ユーザIDの値保持に利用 --}}
            <span id="user_id" data-id=""></span>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
