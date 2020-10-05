
{{--
    マーカーの詳細モーダル
        modalのIDは「detail_modal」で固定
        表示したい項目のIDは「detail_xxxx」とdetail_を必ずつける
        また、classには「detail-view」を必ずつける
--}}
<div class="modal fade" id="marker_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-success modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">マーカー詳細データ</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <ul class="list-inline font-weight-bold">
                    <li class="list-inline-item">マーカー名</li>
                    <li class="list-inline-item"><span id="detail_name" class="detail-view"></span> </li>
                    <li class="list-inline-item">（<span id="detail_status" class="detail-view"></span>）</li>
                </ul>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="item1-tab" data-toggle="tab" href="#item1" role="tab" aria-controls="item1" aria-selected="true">詳細データ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="item2-tab" data-toggle="tab" href="#item2" role="tab" aria-controls="item2" aria-selected="false">所有ユーザ</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- タブ1つ目(ユーザー詳細)-->
                    <div class="tab-pane fade show active" id="item1" role="tabpanel" aria-labelledby="item1-tab">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <dl class="row">
                                    <dt class="col-2 col-sm-4 text-right">画像</dt>
                                    <dd class="col-12 col-sm-8"><img id="detail_image_file" class="detail-view"></dd>
                                </dl>
                            </div>
                            <div class="col-12 col-sm-6">
                                <dl class="row">
                                    <dt class="col-4 text-right">価格(ポイント)</dt>
                                    <dd class="col-8"><span id="detail_price" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">ポイント区分</dt>
                                    <dd class="col-8"><span id="detail_charge_flg" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">概要</dt>
                                    <dd class="col-8"><span id="detail_description" class="detail-view"></span></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    
                    {{-- タブ2つ目(所有ユーザ管理) --}}
                    <div class="tab-pane fade" id="item2" role="tabpanel" aria-labelledby="item2-tab">
                        <table class="table table-striped table-bordered datatable table-sm" id="marker_user_list">
                            <thead>
                                <tr role="row">
                                    <th>履歴ID</th>
                                    <th>ユーザ名</th>
                                    <th>メールアドレス</th>
                                    <th>購入日時</th>
                                    <th>ステータス</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="detail_user_id" value="" />
                <button class="btn btn-secondary" type="button" data-dismiss="modal">閉じる</button>
            </div>
        </div>
        <div>
            {{-- コミュニティIDの値保持に利用 --}}
            <span id="marker_id" data-id=""></span>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
