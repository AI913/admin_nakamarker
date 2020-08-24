@extends('admin.layouts.app')


{{--
    詳細ページ
        modalのIDは「detail_modal」で固定
        表示したい項目のIDは「detail_xxxx」とdetail_を必ずつける
        また、classには「detail-view」を必ずつける
--}}
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-success modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">登録場所一覧</h5>
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
                        <a class="nav-link active" id="item1-tab" data-toggle="tab" href="#item1" role="tab" aria-controls="item1" aria-selected="true">登録場所一覧</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" id="item2-tab" data-toggle="tab" href="#item2" role="tab" aria-controls="item2" aria-selected="false">コミュニティロケーション</a>
                    </li> --}}
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade" id="item2" role="tabpanel" aria-labelledby="item2-tab">
                        <table class="table table-striped table-bordered datatable table-sm" id="user_location_list">
                            <thead>
                                <tr role="row">
                                    <th>ID</th>
                                    <th>マーカー名</th>
                                    <th>場所の名前</th>
                                    <th>登録画像</th>
                                    <th>登録日時</th>
                                    <th>メモ</th>
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
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>


@section('app_js')
    <script src="{{ asset('js/app/user.js') }}?v={{ config('const.app_version') }}"></script>
@endsection