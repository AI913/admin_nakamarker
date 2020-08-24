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
// function customCheck() {
// 	var check = true;
//     var char_count_check = true;

//     //メールアドレスの形式チェック
//     var regexp_email = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/;
//     if (regexp_email.test($('#email').val())) {
//     } else {
//     	$('#email').focus();
//         $('#email').after("<p class='error-area text-danger mb-0'>メールアドレスの形式で入力してください</p>");
//         check = false;
//     }

//     if (!check) {
//         return false;
//     }

//     // パスワードの文字数チェック結果
//     let password_char_count = $('.char-count-text').val().length;
//     // 新規登録時は無条件でチェック
//     // →新規登録時は文字数０の時、requiredのチェックがあるのでいらないが念の為
//     if (register_mode == 'create') {
//         char_count_check = characterCountCheck(password_char_count);
//     }
//     // 編集時はパスワードの文字数が０超のときにチェック
//     if (register_mode == 'edit' && password_char_count > 0) {
//         char_count_check = characterCountCheck(password_char_count);
//     }

//     // アカウントのステータス入力をチェック（アカウント停止時は除く）
//     if ($('#btn_account_stop').text() == 'アカウントの停止' && $('input[type="radio"]:checked').val() == undefined) {
//         $('#status_checked').after("<p class='error-area text-danger mb-0' style='padding-left: 155px'>ステータスを選択してください</p>");
//         return false;
//     }

//     // メールアドレスの重複チェック結果
//     // パスワードの文字数チェックのエラーがある場合、同時に出したいので少し冗長な書き方
//     isDuplicateEmailAjax().done(function(response) {
//         // エラーがない場合falseが返ってくる
//         if(response.is_email) {
//             if (!char_count_check) {
//                 $('#password').focus();
//                 $('#password').after("<p class='error-area text-danger mb-0'>パスワードは６文字以上入力してください</p>");
//                 check = false;
//             }
//             // 重複チェックにエラーがある場合
//             $('#email').focus();
//             $('#email').after("<p class='error-area text-danger mb-0'>メールアドレスが重複しています</p>");
//             check = false;
//         } else {
//             // 重複かつ文字数チェックでエラーがない場合のみsubmit
//             if(char_count_check) {
//             	check = true;
//             } else {
//                 $('#password').focus();
//                 $('#password').after("<p class='error-area text-danger mb-0'>パスワードは６文字以上入力してください</p>");
//                 check = false;
//             }
//         }
//         if (!check) {
//             return false;
//         }else{
//         	$('#main_form').submit();
//         }
//     });
// }

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
        '/ajax/user-points_history',
        {
            'id':       $('#id').val(),
            'name':     $('#name').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'give_point'},
            {data: 'pay_point'},
            {data: 'limit_date'},
            {data: 'user.name'},
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
            // { targets: [5], orderable: false, className: 'text-center', width: '150px'},
            // { targets: [6], orderable: false, className: 'text-center', width: '150px'},
            // { targets: [7], orderable: false, className: 'text-center', width: '150px'},
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
// function getListLink(type, id, link, clazz) {
//     if (type == "location") {
//         return '<a href="'+link+'" class="btn btn-success btn-detail '+clazz+'" data-toggle="tooltip" title="登録場所" data-placement="top" data-id="'+id+'"><i class="fas fa-search fa-fw"></i></a>';
//     }
//     if (type == "community") {
//         return '<a href="'+link+'" class="btn btn-warning text-white '+clazz+'" data-toggle="tooltip" title="参加コミュニティ" data-placement="top"><i class="fas fa-users"></i></a>';
//     }
//     if (type == "edit") {
//         return '<a href="'+link+'" class="btn btn-primary '+clazz+'" data-toggle="tooltip" title="編集" data-placement="top"><i class="fas fa-edit fa-fw"></i></a>';
//     }
// }
