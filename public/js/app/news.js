$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

        // 一覧詳細ボタンクリック
        settingDetailAjax('/admin/news/detail/');
    }

    // 公開フラグのvalue値設定
    if($('#open_flg').prop('checked')) {
        $('#status').val(1);
        // 未入力のバリデーションチェック用クラスを追加
        $('#condition_start_time').addClass('required-text');
        $('#condition_end_time').addClass('required-text');
    } else {
        $('#status').val(0);
        // 未入力のバリデーションチェック用クラスを削除
        $('#condition_start_time').removeClass('required-text');
        $('#condition_end_time').removeClass('required-text');
    }
    $('#open_flg').change(function() {
        if($('#open_flg').prop('checked')) {
            $('#status').val(1);
            // 未入力のバリデーションチェック用クラスを追加
            $('#condition_start_time').addClass('required-text');
            $('#condition_end_time').addClass('required-text');
        } else {
            $('#status').val(0);
            // 未入力のバリデーションチェック用クラスを削除
            $('#condition_start_time').removeClass('required-text');
            $('#condition_end_time').removeClass('required-text');
        }
    })
    //プレビューボタン押下時
    $(document).on('click', '#btn_preview', function(){
        // 画面内容をセット
        setDetailView({
            title: $('#title').val(),
            body: CKEDITOR.instances.body.getData(), // CKエディターからデータを取得するメソッド
            status_name: $('#open_flg').prop('checked') ? '公開' : '非公開',
            image_url: $('#preview').attr('src'),
            open_date: getWeekYmdDate($('#open_date_label').val()),
        });
    });
});

// @1 ファイルドロップ
$(function () {
    // #1 クリックで画像を選択する場合
    $('#drop_area').on('click', function () {
      $('#image').click();
    });

    $('#image').on('change', function () {
      // 画像が複数選択されていた場合(files.length : ファイルの数)
      if (this.files.length > 1) {
        alert('アップロードできる画像は1つだけです');
        $('#image').val('');
        return;
      }

      handleFiles($('#image')[0].files);
    });
    // #1

    // ドラッグしている要素がドロップ領域に入ったとき・領域にある間
    $('#drop_area').on('dragenter dragover', function (event) {
        event.stopPropagation();
        event.preventDefault();
        $('#drop_area').removeClass('dashed'); // 点線の枠を設定したクラスをリセット
        $('#drop_area').addClass('solid');     // 枠を実線にする
    });

    // ドラッグしている要素がドロップ領域から外れたとき
    $('#drop_area').on('dragleave', function (event) {
        event.stopPropagation();
        event.preventDefault();
        $('#drop_area').removeClass('solid'); // 実線の枠を設定したクラスをリセット
        $('#drop_area').addClass('dashed');  // 枠を点線に戻す
    });

    // #2ドラッグしている要素がドロップされたとき
    $('#drop_area').on('drop', function (event) {
        event.preventDefault();

        $('#image')[0].files = event.originalEvent.dataTransfer.files;

        // 画像が複数選択されていた場合
        if ($('#image')[0].files.length > 1) {
            alert('アップロードできる画像は1つだけです');
            $('#image').val('');
            return;
        }

        handleFiles($('#image')[0].files);

    });
    // #2

    // 選択された画像ファイルの操作
    function handleFiles(files) {
        var file = files[0];
        var reader = new FileReader();

        // 画像ファイル以外の場合は何もしない
        // A.indexOf(B)はAにBの値を含むかを判別！含む場合は0以上の値を返し、含まない場合は-1を返す
        if(file.type.indexOf("image") < 0){
            alert('画像ファイル以外はアップロード出来ません');
            return false;
        }

        reader.onload = (function (file) {  // 読み込みが完了したら

            // previeクラスのdivにimgタグを以下のプロパティ付きで実装
            return function(e) {
                $('.preview').empty();
                $('.preview').append($('<img>').attr({
                    src: e.target.result, // readAsDataURLの読み込み結果がresult
                    width: "350px",
                    height: "250px",
                    class: "preview",
                    id: "preview",
                    title: file.name
                }));  // previewに画像を表示
            };
        })(file);

        reader.readAsDataURL(file); // ファイル読み込みを非同期でバックグラウンドで開始

        // 削除フラグを解除
        $('#img_delete').val(0);
    }


    // drop_area以外でファイルがドロップされた場合、ファイルが開いてしまうのを防ぐ
    $(document).on('dragenter', function (event) {
        event.stopPropagation();
        event.preventDefault();
    });
    $(document).on('dragover', function (event) {
        event.stopPropagation();
        event.preventDefault();
    });
    $(document).on('drop', function (event) {
        event.stopPropagation();
        event.preventDefault();
    });
});
// @1


// @2 プレビュー画像削除時の設定
$(function(){
    // 画像のセット
    let outImage = 'images/noImage/no_image.png';

    $('#cancel').on('click', function(){
        $preview = $(".preview");

        // 画像ファイルと既存のプレビューを削除
        $preview.empty();
        $('#image').val(null);
        if($('#image_flg').val()) {
            $('#image_flg').val(null);
        }

        // .prevewの領域の中にロードした画像を表示するimageタグを追加
        $preview.append($('<img>').attr({
            src: outImage,
            width: "350px",
            height: "250px",
            class: "preview",
            id: "preview",
        }));

        $('#drop_area').removeClass('solid'); // 枠を点線に戻す

        // 削除フラグを設定
        $('#img_delete').val(1);
    });
});
// @2

/**
* 一覧詳細(詳細ボタンがあるページは定義する)
* @param data
*/
function setDetailView(data) {
    $('#detail_title').html(data.title);
    $('#detail_status').html(data.status_name);
    $('#detail_image_file').attr('src', data.image_url);
    $('#detail_body').html(data.body);
    $('#detail_open_date').html(data.condition_start_time);
    $('#detail_modal').modal('show');
}

/**
 * 画面固有チェック
 * @returns {boolean}
 */
function customCheck() {

    // 公開フラグの確認アラート
	if($('#status').val() == 0){
        if(confirm('公開ステータスが非公開に設定されています。\n\nこのまま登録しても宜しいでしょうか？')) {
            $('#main_form').submit();
        }
	}else{
        $('#main_form').submit();
	}
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
        '/admin/ajax/news',
        {
            'id': $('#id').val(),
            'title': $('#title').val(),
            'type': $('#type').val(),
            'status': $('#status').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {data: 'title'},

            {
                // サムネイルの画像を表示(モーダル形式)
                data: function (p) {

                    return `
                        <a href="" data-toggle="modal" data-target="#modal${p.id}">
                            <img src="${p.image_url}" height="45" width="65">
                        </a>

                        <div class="modal fade" id="modal${p.id}" tabindex="-1"
                            role="dialog" aria-labelledby="label1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="label1">サムネイル</h5>
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

            {
                data: function(p) {
                    // "非公開"の場合は赤色で表示
                    if(p.status === 0) {
                        return (`<span style='color: red'>${p.status_name}</span>`);
                    }
                    // "公開"の場合は青色で表示
                    return (`<span style='color: blue'>${p.status_name}</span>`);
                }, name: 'status',
            },
            {data: 'start_time', name: 'condition_start_time'},
            {data: 'end_time', name: 'condition_end_time'},
            {data: 'user.name', name: 'update_user_id'},
            // 各操作列
            {
                data: function (p) {
                    // 詳細・編集・削除
                    return getListLink('detail', p.id, '', 'list-button') +
                        getListLink('edit', 0, '/admin/news/edit/'+p.id, 'list-button') +
                        getListLink('remove', p.id, '', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        // 操作列(ボタン等)や画像項目はソート不可・text-centerを付与する
        [
            { targets: [2], orderable: false, className: 'text-center', width: '80px'},
            { targets: [3], orderable: true, className: 'text-center', width: '100px'},
            { targets: [6], orderable: true, className: 'text-center', width: '110px'},
            { targets: [7], orderable: false, className: 'text-center', width: '150px'}
           ],
           search
    );
}
