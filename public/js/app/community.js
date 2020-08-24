$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

        // 一覧詳細ボタンクリック
        settingDetailAjax('/admin/stripe/detail/');
    }

    // 公開フラグのvalue値設定
    $('#open_flg').change(function() {
        if($('#open_flg').prop('checked')) {
            $('#status').val(1);
        } else {
            $('#status').val(0);
        }
    })
    // //プレビュー
    // $('#btn_preview').on('click', function(){
    //     // 画面内容をセット
    //     setDetailView({
    //         "stripe_charge.stripe_code": $('#stripe_code').val(),
    //         "stripe_charge.charge_time": $('#charge_time').val(),
    //         "user.name": $('#name').val(),
    //         "stripe_charge.amount": $('#amount').val(),
    //     });
    // });
});

/**
* 一覧詳細(詳細ボタンがあるページは定義する)
* @param data
*/
function setDetailView(data) {
    $('#detail_stripe_code').html(data.stripe_code);
    $('#detail_charge_time').html(data.charge_time);
    $('#detail_user_name').html(data.user_name);
    $('#detail_amount_format').html(data.amount);
    $('#detail_modal').modal('show');
}

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
        $('#drop_area').addClass('solid');  // 枠を実線にする
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
    let outImage = 'http://nakamarker.localhost/images/noImage/no_image.png';
    
    $('#delete_flg').change(function() {
        // 画像の強制削除フラグ確認
        if($('#delete_flg').prop('checked') === true) {
            outImage = 'http://nakamarker.localhost/images/noImage/out_images.png';
            $('#delete_flg_on').val(true);
        }
        if($('#delete_flg').prop('checked') === false) {
            outImage = 'http://nakamarker.localhost/images/noImage/no_image.png';
            $('#delete_flg_on').val(false);
        }
        $preview = $(".preview");

        // 強制削除の画像以外で画像ファイルがアップロードされていないことが条件
        if($('#image').val() === "" && $('#image_flg').val() === "") {
            $preview.append($('<img>').attr({
                src: outImage,
                width: "350px",
                height: "250px",
                class: "preview",
            }));
        }
    })

    $('#cancel').on('click', function(){
        $preview = $(".preview");

        // 画像ファイルと既存のプレビューを削除
        $preview.empty();
        $('#image').val(null);
        if($('#image_flg').val()) {
            $('#image_flg').val(null);

            // 編集時に"強制削除フラグ"を一度もタッチしなかった場合の処理
            if($('#delete_flg_on').val() == "") {
                $('#delete_flg_on').val(false);
            }
        }

        // .prevewの領域の中にロードした画像を表示するimageタグを追加
        $preview.append($('<img>').attr({
            src: outImage,
            width: "350px",
            height: "250px",
            class: "preview",
        }));

        $('#drop_area').removeClass('solid'); // 枠を点線に戻す

        // 削除フラグを設定
        $('#img_delete').val(1);
    });
});
// @2

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
        '/ajax/community',
        {
            'id': $('#id').val(),
            'name': $('#name').val(),
            'status':   $('#status').val(),
        },
        // 各列ごとの表示定義
        [

            {data: 'id'},
            {data: 'created_at'},
            {
                // コミュニティイメージの画像を表示(モーダル形式)
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
                                        <h5 class="modal-title" id="label1">マーカーイメージ</h5>
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
            {data: 'description'},
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
            // 各操作列
            {
                data: function (p) {
                    // 参加メンバー・編集ボタンの設定
                    return getListLink('community', p.id ,'/community/detail/'+p.id, 'list-button') +
                    getListLink('edit', p.id, '/community/edit/'+p.id, 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        // 操作列(ボタン等)や画像項目はソート不可・text-centerを付与する
        [
            { targets: [2], orderable: false, className: 'text-center', width: '150px'},
            { targets: [5], orderable: false, className: 'text-center', width: '100px'},
            { targets: [6], orderable: false, className: 'text-center', width: '100px'},
            { targets: [7], orderable: false, className: 'text-center', width: '150px'},
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
    if (type == "community") {
        return '<a href="'+link+'" class="btn btn-warning text-white '+clazz+'" data-toggle="tooltip" title="参加ユーザ" data-placement="top"><i class="fas fa-users"></i></a>';
    }
    if (type == "edit") {
        return '<a href="'+link+'" class="btn btn-primary '+clazz+'" data-toggle="tooltip" title="編集" data-placement="top"><i class="fas fa-edit fa-fw"></i></a>';
    }
}