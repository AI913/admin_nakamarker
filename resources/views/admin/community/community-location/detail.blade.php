
{{--
    コミュニティのロケーションモーダル
        modalのIDは「detail_modal」で固定
        表示したい項目のIDは「detail_xxxx」とdetail_を必ずつける
        また、classには「detail-view」を必ずつける
--}}

    {{-- 登録場所の備考モーダル --}}
    <div class="modal fade" id="community_location_modal" tabindex="-1"
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
            <!-- /.modal-content-->
        </div>
        <!-- /.modal-dialog-->
    </div>
