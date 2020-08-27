$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);
    }

});

/**
 * DataTables一覧初期化
 */
function initList(search) {
    
    // DataTable設定
    settingDataTables(
        // 取得
        // tableのID
        'main_list',
        // 取得URLおよびパラメタ
        '/ajax/community-history',
        {
            'id':       $('#id').val(),
            'name':     $('#name').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'community.name'},
            {data: 'user.name'},
            {data: 'status_name'},
            {data: 'updated_at'},
            {
                data: function (p) {
                    // 登録場所・参加コミュニティ・編集ボタンの設定
                    return getListLink('edit', p.id, '', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        [
            // ボタン部分
            { targets: [3], orderable: false, className: 'text-center', width: '150px'},
            { targets: [5], orderable: false, className: 'text-center', width: '150px'},
        ],
        search
    );
}

/**
 * 一覧操作列リンク作成
 * @param type
 * @param id
 * @param link
 * @returns {string}
 */
function getListLink(type, id, link, clazz) {
//     if (type == "location") {
//         return '<a href="'+link+'" class="btn btn-success btn-detail '+clazz+'" data-toggle="tooltip" title="登録場所" data-placement="top" data-id="'+id+'"><i class="fas fa-search fa-fw"></i></a>';
//     }
//     if (type == "community") {
//         return '<a href="'+link+'" class="btn btn-warning text-white '+clazz+'" data-toggle="tooltip" title="参加コミュニティ" data-placement="top"><i class="fas fa-users"></i></a>';
//     }
    if (type == "edit") {
        return '<a href="'+link+'" class="btn btn-primary '+clazz+'" data-toggle="tooltip" title="編集" data-placement="top"><i class="fas fa-edit fa-fw"></i></a>';
    }
}
