
<div class="modal fade" id="points_history_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ポイント詳細 & 履歴一覧</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <ul class="list-inline font-weight-bold">
                    <li class="list-inline-item">ユーザ名</li>
                    <li class="list-inline-item"><span id="detail_name" class="detail-view"></span></li>
                    <li class="list-inline-item">（<span id="detail_status" class="detail-view"></span>）</li>
                </ul>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="item1-tab" data-toggle="tab" href="#item1" role="tab" aria-controls="item1" aria-selected="true">詳細データ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="item2-tab" data-toggle="tab" href="#item2" role="tab" aria-controls="item2" aria-selected="false">ポイント履歴</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- タブ1つ目 --}}
                    <div class="tab-pane fade show active" id="item1" role="tabpanel" aria-labelledby="item1-tab">
                        <div class="row">
                            <div class="col-6">
                                <dl class="row">
                                    <dt class="col-4 text-right">付与種別</dt>
                                    <dd class="col-8"><span id="detail_type" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">付与ポイント</dt>
                                    <dd class="col-8"><span id="detail_give_points" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">消費ポイント</dt>
                                    <dd class="col-8"><span id="detail_pay_points" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">イベント発生日時</dt>
                                    <dd class="col-8"><span id="detail_created_at" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">有料フラグ</dt>
                                    <dd class="col-8"><span id="detail_charge_type" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">備考</dt>
                                    <dd class="col-8"><span id="detail_memo" class="detail-view"></span></dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    {{-- タブ2つ目 --}}
                    <div class="tab-pane fade" id="item2" role="tabpanel" aria-labelledby="item2-tab">
                        <table class="table table-striped table-bordered datatable table-sm" id="user_points_list">
                            <thead>
                                <tr role="row">
                                    <th>ID</th>
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
                                    <select id="select_charge_type" class="form-control">
                                        <option value="" disabled selected>{{ __('有料フラグ') }}</option>
                                        <option value="1">{{ config('const.charge_type_off_name') }}</option>
                                        <option value="2">{{ config('const.charge_type_on_name') }}</option>
                                    </select>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-primary" id="detail_point_submit" data-id="">付与</button>
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
                <button class="btn btn-secondary point_modal" type="button" data-dismiss="modal">閉じる</button>
            </div>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
