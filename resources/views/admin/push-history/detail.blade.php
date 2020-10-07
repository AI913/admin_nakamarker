
{{--
    詳細情報のモーダル
        modalのIDは「detail_modal」で固定
        表示したい項目のIDは「detail_xxxx」とdetail_を必ずつける
        また、classには「detail-view」を必ずつける
--}}
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-success modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">プッシュ通知詳細データ</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <ul class="list-inline font-weight-bold">
                    <li class="list-inline-item">タイトル</li>
                    <li class="list-inline-item"><span id="detail_title" class="detail-view"></span></li>
                </ul>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="item1-tab" data-toggle="tab" href="#item1" role="tab" aria-controls="item1" aria-selected="true">詳細データ</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="item1" role="tabpanel" aria-labelledby="item1-tab">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <dl class="row">
                                    <dt class="col-4 text-right">送信対象</dt>
                                    <dd class="col-8"><span id="detail_type" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">送信予約日時</dt>
                                    <dd class="col-8"><span id="detail_reservation_date" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">配信ステータス</dt>
                                    <dd class="col-8"><span id="detail_status" class="detail-view"></span></dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4 text-right">備考</dt>
                                    <dd class="col-8"><span id="detail_memo" class="detail-view"></span></dd>
                                </dl>
                            </div>
                            <div class="col-12 col-sm-6">
                                <dl class="row">
                                    <dt class="col-4 text-right">本文</dt>
                                    <dd class="col-8"><span id="detail_content" class="detail-view"></span></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
