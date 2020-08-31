
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
                <h5 class="modal-title">プレビュー</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <ul class="list-inline font-weight-bold">
                    <li class="list-inline-item">タイトル</li>
                    <li class="list-inline-item"><span id="detail_title" class="detail-view"></span></li>
                    <li class="list-inline-item">（<span id="detail_status" class="detail-view"></span>）</li>
                </ul>

                <ul class="nav nav-tabs" role="tablist">
                    {{-- <li class="nav-item">
                        <a class="nav-link" id="item2-tab" data-toggle="tab" href="#item2" role="tab" aria-controls="item2" aria-selected="false">コミュニティロケーション</a>
                    </li> --}}
                </ul>
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="modal-body">
                                <div class="text-center font-weight-bold font-3xl mt-2 mb-3 text-secondary">
                                    内容
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <img id="detail_image_file" src="" width="400" height="300" class="detail-view">
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="detail_body" class="detail-view text-gray"></p>
                                    </div>
                                </div>
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
