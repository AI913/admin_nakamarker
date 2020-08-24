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
 * ステータス変更イベント
 * @param status
 */
function changeStatus(status) {
    if (status == user_admin_store_sp || status == user_admin_store_op) {
        $('#company_area').show();
        $('#division_area').show();
        $('#store_area').show();
    } else if(status == user_admin_division) {
        $('#company_area').show();
        $('#division_area').show();
        $('#store_area').hide();
    } else if(status == user_admin_company) {
        $('#company_area').show();
        $('#division_area').hide();
        $('#store_area').hide();
    } else {
        $('#company_area').hide();
        $('#division_area').hide();
        $('#store_area').hide();
    }
}

/**
 * 画面固有チェック
 * @returns {boolean}
 */
function customCheck() {
    var char_count_check = true;

    // パスワードの文字数チェック結果
    let password_char_count = $('.char-count-text').val().length;
    // 新規登録時は無条件でチェック
    // →新規登録時は文字数０の時、requiredのチェックがあるのでいらないが念の為
    if (register_mode == 'create') {
        char_count_check = characterCountCheck(password_char_count);
    }
    // 編集時はパスワードの文字数が０超のときにチェック
    if (register_mode == 'edit' && password_char_count > 0) {
        char_count_check = characterCountCheck(password_char_count);
    }
    if (char_count_check == false) {
        $('#password').focus();
        $('#password').after("<p class='error-area text-danger mb-0'>パスワードは６文字以上入力してください</p>");
    }
    // 会社選択必須(必要な場合のみ)
    if ($('#company_area').css('display') != "none" && !isInputValue($('#company_id'))) {
        $('#company_id').focus();
        $('#company_id').after("<p class='error-area text-danger mb-0'>"+$('#company_id').attr("data-title")+"は必須入力です</p>");
        char_count_check = false;
    }
    // 事業部選択必須(必要な場合のみ)
    if ($('#division_area').css('display') != "none" && !isInputValue($('#division_id'))) {
        $('#division_id').focus();
        $('#division_id').after("<p class='error-area text-danger mb-0'>"+$('#division_id').attr("data-title")+"は必須入力です</p>");
        char_count_check = false;
    }
    // 店舗選択必須(必要な場合のみ)
    if ($('#store_area').css('display') != "none" && !isInputValue($('#store_id'))) {
        $('#store_id').focus();
        $('#store_id').after("<p class='error-area text-danger mb-0'>"+$('#store_id').attr("data-title")+"は必須入力です</p>");
        char_count_check = false;
    }
    // ステータス未選択
    if ($('[name=status]:checked').val() == undefined) {
        $('.status-area').append("<p class='error-area text-danger mb-0'>ステータスを選択してください</p>");
        char_count_check = false;
    }

    if (char_count_check) {
        // メールアドレスの重複チェック結果
        // パスワードの文字数チェックのエラーがある場合、同時に出したいので少し冗長な書き方
        isDuplicateEmailAjax().done(function(response) {
            // エラーがない場合falseが返ってくる
            if(response.is_email) {
                // 重複チェックにエラーがある場合
                $('#email').focus();
                $('#email').after("<p class='error-area text-danger mb-0'>メールアドレスが重複しています</p>");
            } else {
                $('#main_form').submit();
            }
        });
    }

}

/**
 * DataTables一覧初期化
 */
function initList(search) {
    // DataTable設定
    settingDataTables(
        // 取得
        'main_list',
        '/admin/ajax/system_user',
        {
            'id': $('#id').val(),
            'name': $('#name').val(),
            'email': $('#email').val(),
            'status': $('#status').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'name'},
            {
                data: function (p) {
                    if (p.company_id == null) {
                        return "----";
                    } else {
                        return p.company.name + '｜' + p.division.name + '｜' + p.store.name;
                    }
                }
            },
            {data: 'email'},
            {data: 'status_name'},
            {data: 'login_time'},
            {data: 'update_user.name', name:'update_user.id'},
            {
                data: function (p) {
                    // 詳細・編集・削除
                    return getListLink('edit', 0, '/admin/system_user/edit/'+p.id, 'list-button') +
                        getListLink('remove', p.id, '', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        [
            // ボタン部分
            { targets: [7], orderable: false, className: 'text-center', width: '150px'}
        ],
        search
    );
}

/**
 * 新規登録と編集時のメールアドレスの重複チェック
 * @param data
 */
function isDuplicateEmailAjax() {
    let email = $('#email').val()
    // 編集時に対象者除外用
    let id;
    if (register_mode == 'edit') {
        id = $('#email').attr('data-id');
    }

    return $.ajax({
        url:    '/admin/ajax/is_duplicate_email',
        type:   'POST',
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data:   {'email': email, 'id': id}
    })
};

/**
 * 新規登録と編集時のパスワードの文字数チェック
 * @param {*} count 
 * @returns {boolean}
 */
function characterCountCheck(count) {
    let check = true;
    // 逆順での処理(下の項目から、上へ)
    $($('.char-count-text').get().reverse()).each(function(index, elm){
        if (count < 6) {
            check = false;
        }
    });
    // エラーがある場合は処理しない
    if (!check) {
        return false;
    }
    return true;
};
