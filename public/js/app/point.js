$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

    }
});


/**
 * 画面固有チェック
 * @returns
 */
function customCheck() {
	let check = true;
	//日時の整合性チェック
	if ($('#start_date').val() >= $('#end_date').val()) {
		$('#start_date').before("<p class='error-area text-danger mb-0'>"+"終了日時が開始日時より前になっています</p>");
		$('#end_date').focus();
		return false;
	}
	// 	日時重複チェック結果
    isDuplicateStampDateAjax().done(function(response) {
    	console.log(response.is_date);
    	//重複していない時
    	if (response.is_date == 0){
    		check = true;
    	//開始日時が重複している時
    	}else if (response.is_date == 1){
    		$('#start_date').before("<p class='error-area text-danger mb-0'>"+"この開始日時の指定範囲内にすでに登録のデータがあります</p>");
    		$('#start_date').focus();
    		check = false;
    	//終了日時が重複している時
    	}else if (response.is_date == 2){
    		$('#end_date').before("<p class='error-area text-danger mb-0'>"+"この終了日時の指定範囲内にすでに登録のデータがあります</p>");
    		$('#end_date').focus();
    		check = false;
    	//両方重複している時
    	}else if (response.is_date == 3){
    		$('#start_date').before("<p class='error-area text-danger mb-0'>"+"この開始日時と終了日時の指定範囲内にすでに登録のデータがあります</p>");
    		$('#start_date').focus();
    		check = false;
    	//範囲の中にすでに日時が登録されている時
    	}else if (response.is_date == 4){
			$('#start_date').before("<p class='error-area text-danger mb-0'>"+"この開始日時と終了日時の指定範囲の間で、すでに登録されているデータがあります</p>");
			$('#start_date').focus();
			check = false;
    	}

        if (!check) {
            return false;
        }else{
        	$('#main_form').submit();
        }
    })
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
        '/admin/ajax/store/'+store_id+'/point',
        {
            'id': $('#id').val(),
            'name': $('#name').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'name'},
            {data: 'start_date'},
            {data: 'end_date'},
            {data: 'point_type_name', name: 'point_type'},
            {data: 'point'},
            // 各操作列
            {
                data: function (p) {
                    if (p.limit_day == 0 || p.limit_day == null || p.limit_day == undefined || p.limit_day == "") {
                        return '無制限';
                    } else {
                        return p.limit_day + '日間';
                    }
                }
            },
            {data: 'update_user.name', name:'update_user.id'},

            // 各操作列
            {
                data: function (p) {
                    // 詳細・編集・削除
                    if (p.default_flg) {
                        return getListLink('edit', 0, '/admin/store/'+store_id+'/point/edit/'+p.id, 'list-button') +
                        getListLink('remove', p.id, '', 'list-button disabled');
                    }
                    return getListLink('edit', 0, '/admin/store/'+store_id+'/point/edit/'+p.id, 'list-button') +
                        getListLink('remove', p.id, '', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        // 操作列(ボタン等)や画像項目はソート不可・text-centerを付与する
        [
            { targets: [8], orderable: false, className: 'text-center', width: '110px'}
        ],
        search
    );
};

/**
 * 新規登録と編集時の日時の重複チェック
 * @param data
 */
function isDuplicateStampDateAjax() {
    let start_date = $('#start_date').val();
    let end_date = $('#end_date').val();
    // 編集時に対象店舗ポイント除外用
    let point_id = $('#id').val();
    return $.ajax({
        url:    '/admin/ajax/store/'+store_id+'/point/check_date',
        type:   'POST',
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data:   {'start_date': start_date, 'end_date': end_date, 'store_id': store_id, 'point_id': point_id},
    })
};

/**
 * DataTables後処理(色替え処理)
 */
function afterSettingDataTables() {
    let table = $('#main_list').DataTable();
    // 変更済みフラグ
    var changeFlg = false;

    // (1)登録ポイントのいずれかの範囲内かどうかチェックする
    table.rows().data().each(function(val, idx) {
        // すでに変更済みは処理しない
        if (changeFlg) { return true; }
        // 変更対象かどうか判定する
        if (isChangeColor(val)) {
            $('td', $('#main_list tr')[idx+1]).css('background-color', '#fff0f5' );
            changeFlg = true;
        };
    });
    if (!changeFlg) {
        table.rows().data().each(function(val, idx) {
            // デフォルトポイントかどうか判定する
            if (val.default_flg == 1) {
                // 登録店舗すべてのレコードを再度見に行く必要がある
                // ページング、ソート対策
                try {
                    isDefaultStamp().done(function(response){
                        if (response.status == true) {
                            $('td', $('#main_list tr')[idx+1]).css('background-color', '#fff0f5' );
                        }
                    });
                } catch (e) {
                }
                return false;
            };
        });
    }
}
/**
 * 現在選択店舗の登録ポイントすべてのデータから、デフォルトポイントが対象かどうかチェックする
 * @param data
 */
function isDefaultStamp() {
    let start_date = $('#start_date').val();
    let end_date = $('#end_date').val();
    // 編集時に対象店舗ポイント除外用
    let point_id = $('#id').attr('value');
    return $.ajax({
        url:    '/admin/ajax/store/'+store_id+'/point/is_default_point',
        type:   'POST',
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    })
};
/**
 * 背景色を変更するかどうか
 * @param val
 * @returns {boolean}
 */
function isChangeColor(val) {
    let now = getNow();
    if (val.start_date <= now && val.end_date >= now) {
        return true;
    }
    return false;
}

