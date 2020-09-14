
{{--
    参加ユーザのモーダル
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
                        <a class="nav-link active" id="item1-tab" data-toggle="tab" href="#item1" role="tab" aria-controls="item1" aria-selected="false">参加ユーザリスト</a>
                    </li>
                </ul>
                <div class="tab-content">
                    {{-- タブ1つ目(参加ユーザリスト) --}}
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
                                        <th>操作</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    {{-- ユーザリストの備考モーダル --}}
                    <div class="modal fade" id="community_user_modal" tabindex="-1"
                        role="dialog" aria-labelledby="label1" aria-hidden="true">
                        <div class="modal-dialog modal-success modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="label1">備考</h5>
                                    <button type="button" class="close" id="user_modal_close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="container">
                                        <div class="row">
                                            <dt class="col-2 text-right">内容</dt>
                                        </div>
                                        <div class="row">
                                            <dd class="offset-2 col-8"><span id="detail_user_memo" class="detail-view"></span></dd>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="user_modal_close">Close</button>
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
            {{-- コミュニティIDの値保持に利用 --}}
            <span id="community_id" data-id=""></span>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
