/**
 * 文字カウント(バイト数考慮)
 * @returns {number}
 */
String.prototype.bytes = function () {
    var length = 0;
    for (var i = 0; i < this.length; i++) {
        var c = this.charCodeAt(i);
        if ((c >= 0x0 && c < 0x81) || (c === 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) {
            length += 1;
        } else {
            length += 2;
        }
    }
    return length;
};

/**
 * 全角考慮substr
 * @param text
 * @param len
 * @param truncation
 * @returns {string|*}
 */
function substr(text, len, truncation) {
    if (truncation === undefined) { truncation = ''; }
    var text_array = text.split('');
    var count = 0;
    var str = '';
    for (i = 0; i < text_array.length; i++) {
        var n = escape(text_array[i]);
        if (n.length < 4) count++;
        else count += 2;
        if (count > len) {
            return str + truncation;
        }
        str += text.charAt(i);
    }
    return text;
}

/**
 * 現在の日時を取得する
 * @returns {string}
 */
function getNow() {
    var now = new Date();
    var year = now.getFullYear();
    var mon = ("0" + (now.getMonth()+1)).slice(-2); //１を足すこと
    var day = ("0" + now.getDate()).slice(-2);
    var hour = ("0" + now.getHours()).slice(-2);
    var min = ("0" + now.getMinutes()).slice(-2);
    var sec = ("0" + now.getSeconds()).slice(-2);

    return year + "-" + mon + "-" + day + " " + hour + ":" + min + ":" + sec;
}

$(function() {

    // ajaxトークン初期設定
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Datatables日本語化
    if ($.fn.dataTable) {
        $.extend( $.fn.dataTable.defaults, {
            language: {
                "sEmptyTable":     "データはありません。",
                "sProcessing":   "<img style='width:50px; height:50px;' src='/images/loading/loading.gif' />", // 処理中のロード画面をカスタマイズ
                "sLengthMenu":   "_MENU_ 件表示",
                "sZeroRecords":  "データはありません。",
                "sInfo":         " _TOTAL_ 件中 _START_ から _END_ まで表示",
                "sInfoEmpty":    " 0 件中 0 から 0 まで表示",
                "sInfoFiltered": "（全 _MAX_ 件より抽出）",
                "sInfoPostFix":  "",
                "sSearch":       "検索:",
                "sUrl":          "",
                "oPaginate": {
                    "sFirst":    "先頭",
                    "sPrevious": "前",
                    "sNext":     "次",
                    "sLast":     "最終"
                }
            }
        });
    }

    /**
     * @1 検索処理
     */
    // 検索ボタンクリック
    $('#btn_search').on('click', function () {
        $('#main_list').DataTable().destroy();
        initList(true);
    });
    // 検索対象Class指定項目エンター検索
    $('.search-text').on('keypress', function(e){
        if (e.which == 13) {
            $('#btn_search').click();
            return false;
        }
    });
    $('.search-select').on('change', function(e){
        $('#btn_search').click();
        return false;
    });
    // 検索クリア
    $('#btn_search_clear').on('click', function(){
        $('.search-text').val("");
        $('.search-select').val("");
        $('#main_list').DataTable().destroy();
        initList(true);
    });
    /**
     * // @1 検索処理
     */

    // ツールチップ(操作欄にある各種ボタンのタイトルを浮かび上がらせる)
    $('[data-toggle="tooltip"]').tooltip();

    // Infoモーダル表示制御
    if ($('#info_modal').length > 0) {
        setTimeout(function(){
            $('#info_modal').modal('show');
        },500);
    }

    // Errorモーダル表示制御
    if ($('#error_modal').length > 0) {
        setTimeout(function(){
            $('#error_modal').modal('show');
        },500);
    }

    /**
     * @2 登録・編集画面の設定
     */
    // 登録・ログインボタンクリック
    $('#btn_register').on('click', function() {
        // 画面内必須classを持つ項目の必須チェック
        let check = true;
        $('.error-area').remove();
        // 逆順での処理(下の項目から、上へ)
        $($('.required-text').get().reverse()).each(function(index, elm){
            // elmで指定した項目のエラーがある場合、
            // エラーメッセージを表示する
            if (!isInputValue(elm)) {
                $(elm).focus();
                $(elm).after("<p class='error-area text-danger mb-0'>"+$(elm).attr("data-title")+"は必須入力です</p>");
                check = false;
            }
        })
        // エラーがある場合は処理しない
        if (!check) {
            return false;
        }
        // 数値のみチェック
        $($('.number-only-text').get().reverse()).each(function(index, elm){
            if ($(elm).val() != "" && !isNumber($(elm).val())) {
                // if (!$.isNumeric($(elm).val())) {
                $(elm).focus();
                $(elm).after("<p class='error-area text-danger mb-0'>"+$(elm).attr("data-title")+"は数値(0以上)のみ入力可能です</p>");
                check = false;
            }
        })
        // 数値のみチェック(空白はOK)
        $($('.number-check').get().reverse()).each(function(index, elm){
            if ($(elm).val() != "" && !isNumber($(elm).val())) {
                // if (!$.isNumeric($(elm).val())) {
                $(elm).focus();
                $(elm).after("<p class='error-area text-danger mb-0'>"+$(elm).attr("data-title")+"は数値(0以上)のみ入力可能です</p>");
                check = false;
            }
        })
        // エラーがある場合は処理しない
        if (!check) {
            return false;
        }
        // 各機能jsの固有チェック呼び出し(各テーブルごとに設定)
        customCheck();

        // customCheck側でsubmitする
        return false;
    });

    // 画像アップロード処理(新規作成・編集画面)
    $(".image-select").on("click", function(){
        $(this).next().trigger("click");
    });
    // 画像アップローダーChange(画像ファイル差し替えイベント)
    // ※ #upload_image : input type="file"タグを指す
    $('#upload_image').on('change', function(e){
        changeFileEvent(e, $(this));
    });

    // 一覧データ削除
    $(document).on('click', '.btn-remove', function(){
        // formのhiddenにIDセット
        $('#remove_id').val($(this).attr("data-id"));
        // 確認メッセージ
        $('#confirm_modal').modal('show');
    });

    // 一覧詳細ボタンクリック

    // 削除確認モーダルOKクリック
    $('#btn_remove').on('click', function(){
        $('#remove_form').submit();
    });

    // datepickerクラス設定
    if ($.datetimepicker) {
        $.datetimepicker.setLocale('ja');
        // カレンダー表示設定(時刻付き)
        $('.datetimepicker').datetimepicker({
            format:'Y-m-d H:i',
            autoclose:true,
            step:1
        });
        // カレンダー表示設定(日付のみ)
        $('.datepicker').datetimepicker({
            format:'Y-m-d',
            timepicker:false,
            autoclose:true,
        });
        // カレンダー表示設定(時刻のみ)
        $('.timepicker').datetimepicker({
            format:'H:i',
            datepicker:false,
            autoclose:true,
            step:1
        });
    }
    // カレンダーからの選択のみ有効(キー入力無効)
    $('.datetimepicker .datepicker').on('keypress', function(){
        return false;
    });

    // 残り文字数表示＆制限
    // 属性maxlength設定があるもののみ
    $('input').each(function(idx, elm){
        if($(elm).attr('maxlength') != undefined) {
            $(elm).attr('data-counter-label', "残り{remaining}文字まで入力できます");
            shortAndSweet(elm, {counterClassName: 'sweet-counter'});
            if ($(elm).hasClass('number-only-text')) {
                $(elm).parent().find('.sweet-counter').css('display', 'none');
            }
        }
    });
    $('textarea').each(function(idx, elm){
        if($(elm).attr('maxlength') != undefined) {
            $(elm).attr('data-counter-label', "残り{remaining}文字まで入力できます");
            shortAndSweet(elm, {counterClassName: 'sweet-counter'});
        }
    });
    
    // registerのキャンセルボタンイベント
    $('#btn_cancel').on('click', function(){
        location.href = $(this).data('url');
    });
});
/**
 * // @2 登録・編集画面の設定
 */

/**
 * 指定Textが入力されているかどうか
 * @param elm
 * @returns {boolean}
 */
function isInputValue(elm) {
    if ($(elm).val() == "" || $(elm).val() == null || $(elm).val() == undefined) {
        return false;
    }
    return true;
}


/**
 * 画像選択からの画像表示
 * @param event
 * @param elm
 */
function changeFileEvent(event, elm) {
    var reader = new FileReader();
    var file = $(elm);
    reader.onload = function () {
        $(file).prev().attr("src", reader.result);
    }
    reader.readAsDataURL(event.target.files[0]);
}

/**
 * 文字列内の改行設定
 * @param str
 * @returns {string|*}
 */
function replaceBR(str) {
    if (str == null || str == undefined || str == "") {
        return "";
    }
    return str.replace(/\r?\n/g, '<br>');
}

/**
 * 数値かどうかチェック
 * @param val
 * @returns {*|boolean}
 */
function isNumber(val) {
    var pattern = /^[-]?([1-9]\d*|0)(\.\d+)?$/;

    return pattern.test(val);
}

/**
 * 指定日付の曜日取得
 * @param date
 * @returns {string}
 */
function getWeek(date) {
    // 曜日を表す文字列の配列を作っておく
    var WeekChars = [ "日", "月", "火", "水", "木", "金", "土" ];

    if (date == "" || date == null || date == undefined) {
        var dObj = new Date();
    } else {
        var dObj = new Date(date);
    }

    // 日付オブジェクトから曜日を得る
    var wDay = dObj.getDay();

    return WeekChars[wDay];
}

/**
 * 指定日付の曜日込み年月日取得
 * @param date
 * @returns {string}
 */
function getWeekYmdDate(date) {
    if (date == "" || date == null || date == undefined) {
        var dd = new Date();
    } else {
        var dd = new Date(date);
    }
    // 曜日取得
    let week = getWeek(date);

    var YYYY = dd.getFullYear();
    var MM = dd.getMonth()+1;
    var DD = dd.getDate();

    return YYYY + "年" + MM + "月" + DD + "日" + " (" + week + ")";

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
        return '<a href="javascript:void(0)" class="btn btn-success btn-detail '+clazz+'" data-toggle="tooltip" title="詳細" data-placement="top" data-id="'+id+'"><i class="fas fa-search fa-fw"></i></a>';
    }
    if (type == "edit") {
        return '<a href="'+link+'" class="btn btn-primary '+clazz+'" data-toggle="tooltip" title="編集" data-placement="top"><i class="fas fa-edit fa-fw"></i></a>';
    }
    if (type == "remove") {
        return '<a href="javascript:void(0)" class="btn btn-danger btn-remove '+clazz+'" data-toggle="tooltip" title="削除" data-placement="top" data-id="'+id+'"><i class="fas fa-trash-alt fa-fw"></i></a>';
    }
}

/**
 * 一覧の操作ボタンClickイベント定義(モーダルの表示処理実行)
 * @param url
 */
function settingDetailAjax(url, button) {

    if(button == undefined) {
        button = '.btn-detail';
    }

    $(document).on('click', button, function() {
        // 詳細画面表示クリア
        $('.detail-view').html("");

        // 削除フォームIDをセット
        $.ajax({url: url + $(this).data('id')})
            .done(function(response){
                if (response.status == 1) {
                    // 各機能別jsで定義する
                    setDetailView(response.data, button);
                } else {
                    alert('no data error');
                }
            });
    });
}

/**
 * DataTable各種設定・データ取得
 * @param elm_id  　　 tableのid
 * @param url　　  　　データ取得URL(ajax/~ )
 * @param data　　　　 検索対象データ
 * @param columns　　  各列ごとの表示定義
 * @param columnDefs   各列の装飾
 * @param search
 */
function settingDataTables(elm_id, url, data, columns, columnDefs, search) {
    // DataTables設定
    let table = $('#'+elm_id).dataTable({
        "processing": true,
        "serverSide": true,
        "stateSave":  true,
        "responsive": true,
        "paginate":   true,
        "ajax": {
            type: "get",
            dataType: 'json',
            url: url,
            data: data,
            timeout: 10000,
            error: function (xhr, error, code) {
                if (xhr.status == 401) {
                    alert('ログイン情報が確認できません。ログイン画面へ戻ります');
                    location.href = "/admin";
                } else {
                    alert('データが正常に取得できませんでした');
                }
            },
        },
        "bFilter":    false,
        "columns": columns,
        "order": [],
        "columnDefs": columnDefs,
        // 読み込み完了後イベント
        "initComplete": function( ) {
            // (レスポンシブが利かなくなるので、再定義)
            $(this).css('width', '100%');
            // ツールチップ設定
            $(this).find('[data-toggle="tooltip"]').tooltip();
            document.body.style.cursor = 'auto';

        },
        // 'scrollX'       : true,
        // "fixedColumns":   {
        //     leftColumns: 1
        // },
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            // 色変更フラグが正の行データの背景色変更
            if (aData.change_row_color) {
                $('td', nRow).css('background-color', aData.change_row_color );
            }
        }
    });
    // 検索処理の場合、ページング初期化
    if (search) {
        table.fnPageChange(0);
    }
}

/**
 * 地図のサークルオブジェクト取得
 * @param latlon
 * @param radius
 * @returns {ZDC.Oval}
 */
function getMapCircle(lat, lng, radius) {
    // 指定半径の円を描く
    return new ZDC.Oval({
            latlon: new ZDC.LatLon(lat, lng),
            x: radius,
            y: radius
        },
        {
            strokeColor: '#00FF00',
            strokeWeight: 2,
            fillColor: '#FF0000',
            lineOpacity: 0.4,
            fillOpacity: 0.4,
            circle: true
        }
    );
}
function lock(){
    var guard = document.createElement("div");
    guard.id = "lock";
    document.body.appendChild(guard);
}

function loading(){
    var loading = document.getElementById("loading");
    $('#loading').show();
}

function loading_text(){
    $('#loading_text').removeAttr('style');
}

function loading_finish() {
    $('#lock').remove();
    $('#loading').hide();
    $('#loading_text').hide();
}

