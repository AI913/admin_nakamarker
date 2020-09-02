
{{--
    参加ユーザのモーダル
        modalのIDは「detail_modal」で固定
        表示したい項目のIDは「detail_xxxx」とdetail_を必ずつける
        また、classには「detail-view」を必ずつける
--}}

<div class="modal fade" id="community_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-warning modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">参加ユーザ一覧</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <ul class="list-inline font-weight-bold">
                    <li class="list-inline-item">コミュニティ名</li>
                    <li class="list-inline-item"><span id="detail_name" class="detail-view"></span></li>
                    <li class="list-inline-item">（<span id="detail_status" class="detail-view"></span>）</li>
                </ul>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" id="item2-tab" data-toggle="tab" href="#item2" role="tab" aria-controls="item2" aria-selected="false">コミュニティロケーション</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="item1" role="tabpanel" aria-labelledby="item1-tab">
                        <div>
                            <table class="table table-striped table-bordered datatable table-sm" id="community_user_list">
                                <thead>
                                    <tr role="row">
                                        <th>ID</th>
                                        <th>ユーザ名</th>
                                        <th>メールアドレス</th>
                                        <th>参加日時</th>
                                        <th>アカウント状態</th>
                                        <th>申請状況</th>
                                    </tr>
                                </thead>
                            </table>
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
