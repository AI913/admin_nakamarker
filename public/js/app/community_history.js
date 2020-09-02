$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

        // 一覧詳細ボタンクリック
        settingDetailAjax('/community-history/detail/', '.btn-entry');
    }

    // 申請状況カラムのボタンが押下されたとき
    $(document).on('click', '.btn-status', function(){
        // 申請状況の値を更新
        updateStatus($(this));
    });
});

/**
 * 申請状況の編集処理
 * @param {*} button 
 */
function updateStatus(button) {
    $.ajax({
        url:    '/ajax/community-history/update_status',
        type:   'POST',
        dataType: 'json',
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data:   {
            'id': $(button).data('id'),
            'status': $(button).data('status'),
        }
    }).done(function(response){
        $(button).data('status', response)
        if (response == 1) {
            $(button).removeClass('btn-danger');
            $(button).addClass('btn-info');
            $(button).html('申請中');
        } else if (response == 2) {
            $(button).removeClass('btn-info');
            $(button).addClass('btn-success');
            $(button).html('承認済み');
        } else if (response == 3) {
            $(button).removeClass('btn-success');
            $(button).addClass('btn-danger');
            $(button).html('却下');
        }
    })
}

/**
 * 指定したコミュニティの申請状況を表示
 * @param data
 */
function setDetailView(data, button) {
    // モーダルに表示する会員情報
    $('#detail_name').html(data.name);
    $('#detail_status').html(data.open_flg_name);
    
    if(button == '.btn-entry') {
        if ($.fn.DataTable.isDataTable('#community_entry_list')) {
            $('#community_entry_list').DataTable().destroy();
        }

        // DataTable設定
        settingDataTables(
            // 取得
            // tableのID
            'community_entry_list',
            // 取得URLおよびパラメタ
            '/ajax/community-history/detail/'+ data.id +'/entry_histories',
            {},
            // 各列ごとの表示定義
            [
                {data: 'id'},
                {data: 'user.name'},
                {data: 'user.email'},
                {data: 'status_name'},
                {data: 'updated_at'},
                {data: 'memo'},
            ],
            // 各列ごとの装飾
            [
                // ボタン部分
                { targets: [5], orderable: false, className: 'text-center', width: '150px'},
            ],
            false
        );
        $('#entry_history_modal').modal('show');
    }
}

// /**
//  * DataTables一覧初期化
//  */
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
            {
                data: function (p) {
                    // 申請中・承認済み・却下ボタンの設定
                    if(p.status == 1) {
                        return '<button class="btn btn-info btn-status text-white w-75" data-id="'+ p.id +'" data-status="'+ p.status +'">'+ p.status_name +'</button>';
                    }
                    if(p.status == 2) {
                        return '<button class="btn btn-success btn-status text-white w-75" data-id="'+ p.id +'" data-status="'+ p.status +'">'+ p.status_name +'</button>';
                    }
                    if(p.status == 3) {
                        return '<button class="btn btn-danger btn-status text-white w-75" data-id="'+ p.id +'" data-status="'+ p.status +'">'+ p.status_name +'</button>';
                    }
                }, name: 'status'
            },
            {data: 'community.member'},
            {data: 'updated_at'},
            {
                data: function (p) {
                    // 登録場所・参加コミュニティ・編集ボタンの設定
                    return getListLink('detail', p.id, '', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        [
            // ボタン部分
            { targets: [3], orderable: false, className: 'text-center', width: '150px'},
            { targets: [4], orderable: false, className: 'text-center', width: '150px'},
            { targets: [6], orderable: false, className: 'text-center', width: '150px'},
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
    if (type == "detail") {
        return '<a href="javascript:void(0)" class="btn btn-warning text-white btn-entry '+clazz+'" data-toggle="tooltip" title="詳細" data-placement="top" data-id="'+id+'"><i class="fas fa-users"></i></a>';
    }
    if (type == "edit") {
        return '<a href="javascript:void(0)" class="btn btn-primary btn-entry_update '+clazz+'" data-toggle="tooltip" title="申請状況の更新" data-placement="top" data-id="'+id+'"><i class="fas fa-edit fa-fw"></i></a>';
    }
}
