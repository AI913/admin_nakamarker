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

    $('#main_form').submit();
}

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
            'update_user_name': $('#update_user_name').val(),
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
                    return getListLink('edit', p.id, '/admin/config/edit/'+p.id, 'list-button') +
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
