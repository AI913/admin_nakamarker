$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

        // 登録場所もしくは参加コミュニティボタンをクリック
        $(document).on('focus', '.btn-location', function() {
            settingDetailAjax('/user/detail/', '.btn-location');
        });
        $(document).on('focus', '.btn-community', function() {
            settingDetailAjax('/user/detail/', '.btn-community');
        });
    }

    // アカウント停止処理
    let num = 0;
    if($('#btn_account_stop').text() == 'アカウント停止中'){
        // アカウント停止状態は備考以外の入力を受け付けない
        $("#name").prop("disabled", true);
        $("#email").prop("disabled", true);
        $("#password").prop("disabled", true);
        $("#status1").prop("disabled", true);
        $("#status2").prop("disabled", true);
        $("#status3").prop("disabled", true);
    }
    $('#btn_account_stop').on('click', function(){

        // クリックごとにvalue値を変更
        if($('#btn_account_stop').text() == 'アカウントの停止'){
            $('#status4').val(4);
            $('#btn_account_stop').text('アカウント停止中');
            $('#btn_account_stop').attr('class', 'btn btn-dark text-white width-150 float-right');
            $("#name").prop("disabled", true);
            $("#email").prop("disabled", true);
            $("#password").prop("disabled", true);
            $("#status1").prop("disabled", true);
            $("#status2").prop("disabled", true);
            $("#status3").prop("disabled", true);
            alert('アカウントの停止を確定するには"更新"ボタンを押してください');
        } else {
            // アカウント停止解除後は全項目の入力を受け付ける
            $('#status4').val(null);
            $('#status1').val(1);
            $('#btn_account_stop').text('アカウントの停止');
            $('#btn_account_stop').attr('class', 'btn btn-danger width-150 float-right');
            $("#name").prop("disabled", false);
            $("#email").prop("disabled", false);
            $("#password").prop("disabled", false);
            $("#status1").prop("disabled", false);
            $("#status2").prop("disabled", false);
            $("#status3").prop("disabled", false);
        }
    });
});

/**
 * 登録場所表示
 * @param data
 */
function setDetailView(data, button) {
    // モーダルに表示する会員情報
    $('#detail_name').html(data.name);
    $('#detail_status').html(data.status_name);
    $('#detail_email').html(data.email);
    $('#detail_login_time').html(data.login_time);
    $('#detail_created_at').html(data.created_at);
    $('#detail_user_agent').html(data.user_agent);
    $('#detail_memo').html(data.memo);

    if(button == '.btn-location') {
        // 過去に表示したテーブルのリセット
        if ($.fn.DataTable.isDataTable('#user_location_list')) {
            $('#user_location_list').DataTable().destroy();
        }
        // DataTable設定
        settingDataTables(
            // 取得
            // tableのID
            'user_location_list',
            // 取得URLおよびパラメタ
            `/ajax/user/detail/${data.id}/user_locations`,
            {},
            // 各列ごとの表示定義
            [
                {data: 'location_id'},
                {data: 'marker_name'},
                {data: 'location_name'},
                {
                    // ロケーションイメージの画像を表示(モーダル形式)
                    data: function (p) {
                        
                        return `
                            <a href="" data-toggle="modal" data-target="#modal${p.location_id}">
                                <img src="${p.image_url}" height="45" width="65">
                            </a>
    
                            <div class="modal fade" id="modal${p.location_id}" tabindex="-1"
                                role="dialog" aria-labelledby="label1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="label1">ロケーションイメージ</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                        <img src="${p.image_url}" id="image_modal" height="350" width="450">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                },
                {data: 'created_at'},
                {data: 'memo'},     
            ],
            // 各列ごとの装飾
            [],
            false
        );
        $('#location_modal').modal('show');
    }
    
    if(button == '.btn-community') {
        if ($.fn.DataTable.isDataTable('#user_community_list')) {
            $('#user_community_list').DataTable().destroy();
        }

        // DataTable設定
        settingDataTables(
            // 取得
            // tableのID
            'user_community_list',
            // 取得URLおよびパラメタ
            `/ajax/user/detail/${data.id}/user_communities`,
            {},
            // 各列ごとの表示定義
            [
                {data: 'id'},
                {
                    // コミュニティイメージの画像を表示(モーダル形式)
                    data: function (p) {
                        
                        return `
                            <a href="" data-toggle="modal" data-target="#modal${p.id}">
                                <img src="${p.image_url}" height="45" width="65">
                            </a>
    
                            <div class="modal fade" id="modal${p.id}" tabindex="-1"
                                role="dialog" aria-labelledby="label1" aria-hidden="true">
                                <div class="modal-dialog modal-warning modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="label1">コミュニティイメージ</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                        <img src="${p.image_url}" id="image_modal" height="350" width="450">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                },
                {data: 'name'},
                {data: 'member'},
                {
                    data: function(p) {
                        // "非公開"の場合は赤色で表示
                        if(p.status === 0) {
                            return (`<span style='color: red'>${p.status_name}</span>`);
                        }
                        // "公開"の場合は青色で表示
                        return (`<span style='color: blue'>${p.status_name}</span>`);
                    }
                },
                {data: 'created_at'},
                {data: 'description'},     
            ],
            // 各列ごとの装飾
            [],
            false
        );
        $('#community_modal').modal('show');
    }
}

/**
 * 画面固有チェック
 * @returns {boolean}
 */
function customCheck() {
	var check = true;
    var char_count_check = true;

    //メールアドレスの形式チェック
    var regexp_email = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/;
    if (regexp_email.test($('#email').val())) {
    } else {
    	$('#email').focus();
        $('#email').after("<p class='error-area text-danger mb-0'>メールアドレスの形式で入力してください</p>");
        check = false;
    }

    if (!check) {
        return false;
    }

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

    // アカウントのステータス入力をチェック（アカウント停止時は除く）
    if ($('#btn_account_stop').text() == 'アカウントの停止' && $('input[type="radio"]:checked').val() == undefined) {
        $('#status_checked').after("<p class='error-area text-danger mb-0' style='padding-left: 155px'>ステータスを選択してください</p>");
        return false;
    }

    // メールアドレスの重複チェック結果
    // パスワードの文字数チェックのエラーがある場合、同時に出したいので少し冗長な書き方
    isDuplicateEmailAjax().done(function(response) {
        // エラーがない場合falseが返ってくる
        if(response.is_email) {
            if (!char_count_check) {
                $('#password').focus();
                $('#password').after("<p class='error-area text-danger mb-0'>パスワードは６文字以上入力してください</p>");
                check = false;
            }
            // 重複チェックにエラーがある場合
            $('#email').focus();
            $('#email').after("<p class='error-area text-danger mb-0'>メールアドレスが重複しています</p>");
            check = false;
        } else {
            // 重複かつ文字数チェックでエラーがない場合のみsubmit
            if(char_count_check) {
            	check = true;
            } else {
                $('#password').focus();
                $('#password').after("<p class='error-area text-danger mb-0'>パスワードは６文字以上入力してください</p>");
                check = false;
            }
        }
        if (!check) {
            return false;
        }else{
        	$('#main_form').submit();
        }
    });
}

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
        '/ajax/user',
        {
            'id':       $('#id').val(),
            'name':     $('#name').val(),
            'email':    $('#email').val(),
            'status':   $('#status').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'name'},
            {data: 'email'},
            {data: 'created_at'},
            {data: 'login_time'},
            {
                data: function(p) {
                    // アカウントステータスが"アカウント停止"の場合は赤色で表示
                    if(p.status === 4) {
                        return (`<span style='color: red'>${p.status_name}</span>`);
                    }
                    // それ以外は普通に表示
                    return p.status_name;
                }
            },
            {data: 'memo'},
            
            {
                data: function (p) {
                    // 登録場所・参加コミュニティ・編集ボタンの設定
                    return getListLink('location', p.id, '', 'list-button') +
                           getListLink('community', p.id , '', 'list-button') +
                           getListLink('edit', p.id, '/user/edit/'+p.id, 'list-button') + 
                           getListLink('remove', p.id ,'/community/detail/'+p.id, 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        [
            // ボタン部分
            { targets: [5], orderable: false, className: 'text-center', width: '150px'},
            { targets: [6], orderable: false, className: 'text-center', width: '150px'},
            { targets: [7], orderable: false, className: 'text-center', width: '200px'},
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
        url:    '/ajax/is_duplicate_email',
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

/**
 * 一覧操作列リンク作成
 * @param type
 * @param id
 * @param link
 * @returns {string}
 */
function getListLink(type, id, link, clazz) {
    if (type == "location") {
        return '<a href="javascript:void(0)" class="btn btn-success btn-location '+clazz+'" data-toggle="tooltip" title="登録場所" data-placement="top" data-id="'+id+'"><i class="fas fa-search fa-fw"></i></a>';
    }
    if (type == "community") {
        return '<a href="javascript:void(0)" class="btn btn-warning text-white btn-community '+clazz+'" data-toggle="tooltip" title="参加コミュニティ" data-placement="top" data-id="'+id+'"><i class="fas fa-users"></i></a>';
    }
    if (type == "edit") {
        return '<a href="'+link+'" class="btn btn-primary '+clazz+'" data-toggle="tooltip" title="編集" data-placement="top"><i class="fas fa-edit fa-fw"></i></a>';
    }
    if (type == "remove") {
        return '<a href="javascript:void(0)" class="btn btn-danger btn-remove '+clazz+'" data-toggle="tooltip" title="削除" data-placement="top" data-id="'+id+'"><i class="fas fa-trash-alt fa-fw"></i></a>';
    }
}
