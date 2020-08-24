$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

        // 一覧詳細ボタンクリック
        settingDetailAjax('/admin/information/detail/');

    }

    //プレビュー
    $('#btn_preview').on('click', function(){
        // 画面内容をセット
        setDetailView({
            title: $('#title').val(),
            body: $('#body').val(),
            image_url: $('#view_image').attr('src'),
            open_date: getWeekYmdDate($('#open_date').val()),
        });
    });

    // 配信条件設定
    $(document).on('click', '.btn-condition', function(){
        showCondition($(this).data('id'));
    });
    $(document).on('click', '.radio-age-type', function(){
        if ($(this).val() == 1) {
            $('#age_from_to').hide();
        } else {
            $('#age_from_to').show();
        }
    });

    // 配信条件登録
    $('#btn_condition_register').on('click', function(){
        if (!confirm('配信条件を登録します。よろしいですか？')) {
            return false;
        }
        // 画面ロック
        lock();
        registerCondition($('#information_id').val());
        // ロード画面
        loading_text();
        loading();

    });

    $(document).on('click', '.btn-information-pickup', function(){
        updatePickUp($(this));
    });
});
//公開のラジオボタン選択時日時選択フォーム有効化
$(function() {
    if ($('input[name="open_status"]:checked').val() == 1) {
        $('#open_date_label').append('<span class="text-danger open_asterisk">※</span>');
    }

    $('input[name="open_flg"]').change( function() {
        var open_radio_val = $(this).val();
        //特定のラジオボタンを押した時は無効化を削除
        if(open_radio_val == 1){
            $('#open_date').removeAttr('disabled');
            $('#open_date').addClass("required-text");
            $('#open_date_label').append('<span class="text-danger open_asterisk">※</span>');
            //それ以外のラジオボタンを押した時は無効化を付与
        } else {
            $('#open_date').attr('disabled','disabled');
            $('#open_date').removeClass("required-text");
            $('.open_asterisk').remove();
        }
    });
});
function updatePickUp(button) {
    $.ajax({
        url:    '/admin/ajax/information/pickup/update',
        type:   'POST',
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data:   {
            'information_id': $(button).data('id'),
            'pickup': $(button).data('pickup'),
        }
    }).done(function(response){
        $(button).data('pickup', response)
        if (response == 1) {
            $(button).removeClass('btn-secondary');
            $(button).addClass('btn-info');
            $(button).html('表示する');
        } else {
            $(button).removeClass('btn-info');
            $(button).addClass('btn-secondary');
            $(button).html('表示しない');
        }
    });
}

/**
 * サービス券配信条件登録処理
 * @param service_id
 */
function registerCondition(information_id) {
    try {
        $.ajax({
            url:    '/admin/ajax/information/condition/register',
            type:   'POST',
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data:   {
                'information_id': information_id,
                'status': $('[name=status]:checked').val(),
                'sex': $('[name=sex]:checked').val(),
                'age': $('[name=age]:checked').val(),
                'age_from': $('#age_from').val(),
                'age_to': $('#age_to').val(),
            }
        }).done(function(response){
            loading_finish();
            $('#condition_modal').modal('hide');
            $('#condition_message_body').html(response.message);
            $('#condition_result_modal').modal('show');
            $('#btn_search').trigger('click');
        });
    } catch (e) {
        loading_finish();
        alert(e);
    }
}
/**
 * 配信条件設定表示
 * @param id
 */
function showCondition(id) {
    // 削除フォームIDをセット
    $.ajax({url: '/admin/information/detail/' + id})
    .done(function(response){
        if (response.status == 1) {
            let condition = response.data.condition_list;
            $('#status_'+condition.status).prop('checked', true);
            $('#sex_'+condition.sex).prop('checked', true);
            $('#age_'+condition.age).prop('checked', true);
            if ($('[name=age]:checked').val() == 2) {
                $('#age_from_to').show();
            } else {
                $('#age_from_to').hide();
            }
            $('#age_from').val(condition.age_from);
            $('#age_to').val(condition.age_to);
            $('#information_id').val(response.data.id);
            $('#condition_modal').modal('show');
        } else {
            alert('no data error');
        }
    });
}
/**
* 一覧詳細(詳細ボタンがあるページは定義する)
* @param data
*/
function setDetailView(data) {
    $('#detail_title').html(data.title);
    $('#detail_image_file').attr('src', data.image_url);
    $('#detail_body').html(replaceBR(data.body));
    $('#detail_open_date').html(data.open_date);
    $('#detail_modal').modal('show');

}

/**
 * 画面固有チェック
 * @returns {boolean}
 */
function customCheck() {
    let check = true;
    const check_store_length = $('input[name="store_id[]"]:checked').length;
    if ($('#check_all').length > 0 && check_store_length == 0) {
        $('.store_checkbox').after("<p class='error-area text-danger mb-0'>"+"店舗は一つ以上選択してください</p>");
        $('#check_all').focus();
        check = false;
    }
    if (!check) { return false; }

    // 固有チェック、なければform.submitを行う
    // 問題なければsubmit
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
        '/admin/ajax/information',
        {
            'id': $('#id').val(),
            'title': $('#title').val(),
            'app_view_flg': $('#app_view_flg').val(),
            'push_flg': $('#push_flg').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'title'},

            // 項目を装飾する場合は、data: functionで別途定義
            {
                data: function (p) {
                    return '<img src="' + p.image_url_small + '" height="50">';
                }
            },

            {data: 'open_date'},
            {data: 'status_name', name: 'open_date'},
            {data: 'app_view_flg_name', name: 'app_view_flg'},

            // ピックアップ
            {
                data: function (p) {
                    if (p.pickup_flg == 1) {
                        return '<button class="btn btn-info text-white btn-information-pickup" data-id="'+p.id+'" data-pickup="'+p.pickup_flg+'">表示する</button>';
                    } else {
                        return '<button class="btn btn-secondary text-white btn-information-pickup" data-id="'+p.id+'" data-pickup="'+p.pickup_flg+'">表示しない</button>';
                    }
                }, name: 'pickup_flg'
            },
            
            {data: 'push_flg_name', name: 'push_flg'},

            // 配信条件
            {
                data: function (p) {
                    // 詳細・編集・削除
                    return '<button class="btn btn-warning text-white btn-condition" data-id="'+p.id+'">配信条件設定</button>';
                }
            },
            
            {data: 'update_user.name', name:'update_user.id'},
            
            // 各操作列
            {
                data: function (p) {
                    // 詳細・編集・削除
                    return getListLink('detail', p.id, '', 'list-button') +
                        getListLink('edit', 0, '/admin/information/edit/'+p.id, 'list-button') +
                        getListLink('remove', p.id, '', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        // 操作列(ボタン等)や画像項目はソート不可・text-centerを付与する
        [
            { targets: [2], orderable: false, className: 'text-center', width: '80px'},
            { targets: [8], orderable: false, className: 'text-center', width: '150px'},
            { targets: [6, 10], orderable: false, className: 'text-center', width: '150px'}
           ],
           search
    );
}
