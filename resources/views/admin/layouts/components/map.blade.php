{{--
    地図表示ページ
    ゼンリンの地図呼び出し
--}}

<div class="modal fade" id="map_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-success modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">地図</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- TODO ゼンリン地図表示--}}
                <img src="{{ asset('images/map_sample.png') }}" id="sample_map">
                <div id="ZMap" style="width:750px; height:500px;"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">閉じる</button>
            </div>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
