$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {
        // DataTables初期化
        initList(false);
    }
});
/**
 * 画面固有チェック
 * @returns {boolean}
 */
function customCheck() {
    // 固有チェック、なければform.submitを行う
    // 問題なければsubmit
    var check = true;

    // 共通設定キーの重複チェック結果
    isDuplicateConfigKeyAjax().done(function(response) {
        // エラーがない場合falseが返ってくる
        if(response.is_key) {
            // 重複チェックにエラーがある場合
            $('#key').focus();
            $('#key').after("<p class='error-area text-danger mb-0'>キー名が重複しています</p>");
            check = false;
        }
        // 重複がない場合のみsubmit
        if (!check) {
            return false;
        }else{
        	$('#main_form').submit();
        }
    });
}

/**
 * 共通設定キーの重複チェック
 * @param data
 */
function isDuplicateConfigKeyAjax() {
    let config_key = $('#key').val()
    // 編集時に対象id除外用
    let config_id ;
    if ($('#register_mode').val() == 'edit') {
        config_id = $('#id').attr('value');
    }
    return $.ajax({
        url:    '/admin/ajax/is_duplicate_config_key',
        type:   'POST',
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data:   {'config_key': config_key, 'config_id': config_id}
    })
};

/**
 * 一覧初期化
 */
function initList(search) {
    // DataTable設定
    settingDataTables(
        // 取得
        // tableのID
        'main_list',
        // 取得URLおよびパラメタ
        '/admin/ajax/config',
        {
            'id': $('#id').val(),
            'key': $('#key').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'key'},
            {data: 'value'},
            {data: 'memo'},
            {data: 'update_user.name', name:'update_user.id'},
            // 各操作列
            {
                data: function (p) {
                    // 詳細・編集・削除
                    return getListLink('edit', 0, '/admin/config/edit/'+p.id, 'list-button') +
                        getListLink('remove', p.id, '', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        // 操作列(ボタン等)や画像項目はソート不可・text-centerを付与する
        [
            { targets: [5], orderable: false, className: 'text-center', width: '150px'}
        ],
        search
    );
}
