$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {
        // DataTables初期化
        initList(false);
    }
    // ステータス状態変更イベント
    $('input[name="status"]').on('change', function() {
        changeStatus($(this).val());
    });

    // 編集時、事業部の初期値設定
    if ($('#company_id').val() > 0) {
        $('#company_id').trigger('change');
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
 * DataTables一覧初期化
 */
function initList(search) {
    // DataTable設定
    settingDataTables(
        // 取得
        'main_list',
        '/ajax/push',
        {
            'id': $('#id').val(),
            'title': $('#title').val(),
            'status': $('#status').val(),
            'type': $('#type').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'reservation_date_style', name: 'reservation_date'},
            {data: 'title'},
            {data: 'content'},
            {
                data: function (p) {
                    // 送信ステータスが"送信中"の場合
                    if(p.status == 2) {
                        return `<span style='color: blue'>${p.status_name}</span>`;
                    }
                    // 送信ステータスが"送信済み"の場合
                    if(p.status == 3) {
                        return `<span style='color: green'>${p.status_name}</span>`;
                    }
                    // 送信ステータスが"送信エラー"の場合
                    if(p.status == 9) {
                        return `<span style='color: red'>${p.status_name}</span>`;
                    }
                    // 送信ステータスが"送信前"の場合
                    return p.status_name;
                }, name: 'status'
            },
            {data: 'type_name', name:'type'},
            {
                data: function (p) {
                    // 詳細・編集・削除
                    return getListLink('detail', p.id, '', 'list-button') +
                           getListLink('edit', 0, '/push/edit/'+p.id, 'list-button') +
                           getListLink('remove', p.id, '', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        [
            // ボタン部分
            { targets: [1], orderable: true, className: 'text-left', width: '170px'},
            { targets: [2], orderable: true, className: 'text-left', width: 'auto'},
            { targets: [3], orderable: false, className: 'text-left', width: 'auto'},
            { targets: [5], orderable: true, className: 'text-center', width: '100px'},
            { targets: [6], orderable: false, className: 'text-center', width: '150px'},
        ],
        search
    );
}

