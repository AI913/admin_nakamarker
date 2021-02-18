$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

        // 一覧詳細ボタンクリック
        // settingDetailAjax('/user-points-history/detail/', '.btn-points');
    }

    // ポイントを編集する場合はポイントIDをセット
    $(document).on('click', '.btn-edit-points', function(){
        $('input[name="id"]').val($(this).data('id'));
        // 編集欄のボタンが押された場合はボタンテキストを変更する
        if($('input[name="id"]').val() != '') {
            $('.edit_point_label').text('ポイントの更新（ID：'+ $(this).data('id') +'）');
            $('#detail_point_submit').text('更新');
        }
    });

    // "閉じる"ボタン押下時にポイント入力フォームの値をリセットする
    $(document).on('click', '.point_modal', function(){
        $('#create_point').val('');
        $('#select_point_type').val(null);
        $('#select_charge_type').val(null);
        $('input[name="id"]').val('');
        $('.edit_point_label').text('ポイントの付与（新規）');
        $('#detail_point_submit').text('付与');
    });
    // リセットボタン押下時にポイント入力フォームの値をリセットする
    $(document).on('click', '#detail_point_reset', function(){
        $('#create_point').val('');
        $('#select_point_type').val(null);
        $('#select_charge_type').val(null);
        $('input[name="id"]').val('');
        $('.edit_point_label').text('ポイントの付与（新規）');
        $('#detail_point_submit').text('付与');
    });
});

/**
 * ポイント履歴情報表示 & ポイント付与欄表示
 * @param data
 */
function setDetailView(data, button) {
    // モーダルに表示する会員情報
    $('#detail_name').html(data.user.name);
    $('#detail_status').html(data.user.status_name);
    $('#detail_type').html(data.type);
    $('#detail_give_points').html(data.give_point);
    $('#detail_pay_points').html(data.pay_point);
    $('#detail_created_at').html(data.created_at);
    $('#detail_charge_type').html(data.charge_name);
    $('#detail_memo').html(data.memo);
    $('#detail_point_submit').attr('data-id', data.user_id); // ポイント付与フォームで利用

    if(button == '.btn-points') {
        if ($.fn.DataTable.isDataTable('#user_points_list')) {
            $('#user_points_list').DataTable().destroy();
        }
        setPointTable(data.id);
        $('#points_history_modal').modal('show');
    }
}

/**
 * ユーザごとのポイント履歴テーブルを生成
 * @param id 
 */
function setPointTable(id) {
    // DataTable設定
    settingDataTables(
        // 取得
        // tableのID
        'user_points_list',
        // 取得URLおよびパラメタ
        '/user-points-history/detail/'+ id +'/point_histories',
        {},
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'type_name'},
            {data: 'give_point'},
            {data: 'pay_point'},
            {data: 'created_at'},
            {
                data: function(p) {
                    // 有料フラグが"有料"の場合は赤色で表示
                    if(p.charge_type === 2) {
                        return ('<span style="color: red">'+ p.charge_name +'</span>');
                    }
                    // それ以外は普通に表示
                    return p.charge_name;
                }
            },
            // ポイントの編集ボタン
            {
                data: function (p) {
                    return getListLink('edit-point', p.id, '', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        [
            // ボタン部分
            { targets: [6], orderable: false, className: 'text-center', width: '120px'},
        ],
        false
    );
}

/**
 * 画面固有チェック
 * @returns {boolean}
 */
$(function(){

    // ポイント付与のバリデーションチェック
    $(document).on("click", '#detail_point_submit', function(){
        // 数値チェック
        if (!Number($('#create_point').val())) {
            alert('ポイントが正しくありません');
            $('#create_point').focus();
            return false;
        }
        // nullチェック
        if ($('#select_point_type').val() == null) {
            alert('付与種別が正しくありません');
            $('#select_point_type').focus();
            return false;
        }
        // nullチェック
        if ($('#select_charge_type').val() == null) {
            alert('有料フラグが正しくありません');
            $('#select_point_type').focus();
            return false;
        }
        updatePoints();
    });
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
        '/ajax/user-points-history',
        {
            'id':          $('#id').val(),
            'type':        $('#type').val(),
            'charge_type':  $('#charge_type').val(),
            'name':        $('#name').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'type_name'},
            {data: 'user.name'},
            {data: 'give_point'},
            {data: 'pay_point'},
            {data: 'created_at'},
            {
                data: function(p) {
                    // 有料フラグが"有料"の場合は赤色で表示
                    if(p.charge_type === 2) {
                        return ('<span style="color: red">'+ p.charge_name +'</span>');
                    }
                    // それ以外は普通に表示
                    return p.charge_name;
                }
            },
            {
                data: function(p) {
                    // アカウントステータスが"アカウント停止"の場合は赤色で表示
                    if(p.user.status === 4) {
                        return (`<span style='color: red'>${p.user.status_name}</span>`);
                    }
                    // それ以外は普通に表示
                    return p.user.status_name;
                }
            },
            {data: 'memo'},
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
            { targets: [7], orderable: false, className: 'text-center', width: '120px'},
            { targets: [8], orderable: false, className: 'text-center', width: '200px'},
            { targets: [9], orderable: false, className: 'text-center', width: '120px'},
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
    if (type == "edit") {
        return '<a href="javascript:void(0)" class="btn btn-primary btn-points '+clazz+'" data-toggle="tooltip" title="ポイント付与" data-placement="top" data-id="'+id+'"><i class="fas fa-edit fa-fw"></i></a>';
    }
    if (type == "edit-point") {
        return '<a href="javascript:void(0)" class="btn btn-primary btn-edit-points '+clazz+'" data-toggle="tooltip" title="編集" data-id="'+id+'"><i class="fas fa-edit fa-fw"></i></a>';
    }
}
