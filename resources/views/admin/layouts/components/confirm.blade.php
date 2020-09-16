{{--
    確認メッセージ呼び出し方

    @include('admin.layouts.confirm', ['id' => 'confirm_modal', 'title' => 'サンプル削除確認', 'message' => '対象データを削除します。よろしいですか？', 'btn_id' => 'btn_delete'])
        id:         モーダルのID
            一画面で複数Modalを必要とする場合に、ID分けのため用意
        title:      モーダルタイトル
        message:    モーダルメッセージ
        btn_id:     モーダルボタンID
            common.jsで「btn_delete」処理用意。それ以外の処理の場合は、各画面のjsに記述する

--}}

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-danger modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <strong>{{ $message }}</strong>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary width-100" type="button" id="{{ $btn_id }}">OK</button>
                <button class="btn btn-secondary width-100" type="button" data-dismiss="modal">キャンセル</button>
            </div>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>