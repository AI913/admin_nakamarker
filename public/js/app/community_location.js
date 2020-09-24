$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);
    }
    
    /* 
     *  "備考"ボタンが押下されたとき
     */
    $(document).on('click', '.btn-location',function(){
        let location_id = $(this).data('id');
        let url = window.location.pathname;
        setLocationDetail(location_id, url);
    });

    /* 
     *  "新規登録"ボタンが押下されたとき
     */
    $(document).on('click', '.btn-location_create_link',function(){
        let url = window.location.pathname;
        location.href = `${url}/create`;
    });

    /* 
     *   モーダルの終了処理
     */
        // 登録情報の備考
        $(document).on('click', '#location_modal_close', function(){
            $('#community_location_modal').modal('hide');
        });

});

/**
 * "登録場所の備考情報"モーダルの表示処理
 * @param id 
 */
function setLocationDetail(location_id, url) {
    // 削除フォームIDをセット
    $.ajax({url: `/ajax${url}/detail/${location_id}`})
    .done(function(response){
        if (response.status == 1) {
            $('#detail_location_memo').html(response.data.memo);

            // 備考モーダルの表示
            $('#community_location_modal').modal('show');
        } else {
            alert('no data error');
        }
    });
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
	var check = true;

    $('#main_form').submit();

}
/**
 * 一覧初期化
 */
function initList(search) {
    // api通信用にURLを取得
    let url = window.location.pathname;

    // DataTable設定
    settingDataTables(
        // 取得
    	'main_list',
        `/ajax${url}`,
        {
            'id': $('#id').val(),
            'marker_name': $('#marker_name').val(),
            'name': $('#name').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'location_id'},
            {data: 'marker_name'},
            {data: 'location_name'},
            {
                // ロケーションイメージの画像を表示(モーダル形式)
                data: function (p) {
                    
                    return `
                        <a href="" data-toggle="modal" data-target="#location_modal${p.location_id}">
                            <img src="${p.image_url}" id="location_image" data-id="${p.location_id}" height="45" width="65">
                        </a>

                        <div class="modal fade" id="location_modal${p.location_id}" tabindex="-1"
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
            {data: 'user_name'},
            {data: 'created_at_style', name: 'created_at'},
            // GoogleMapのリンクを埋め込み
            {
                data: function (p) {
                    // ロケーション情報を埋め込んだGoogle MapのURLを変数に代入
                    let url = `https://www.google.com/maps?q=${p.latitude},${p.longitude}`;
                    // 登録場所の備考ボタン・削除ボタンの設定(備考はデータがあるときのみ表示)
                    return getListLink('map', p.location_id, url, 'list-button');
                }
            },
            {
                data: function (p) {
                    let url = window.location.pathname;

                    // 詳細・編集・削除
                    return getListLink('location', p.location_id, '', 'list-button') +
                           getListLink('edit', p.location_id, `${url}/edit/${p.location_id}`, 'list-button') +
                           getListLink('remove_modal', p.community_id, `${url}/remove`, 'list-button');
                }

            }
        ],
        // 各列ごとの装飾
        [
            { targets: [1], orderable: false, width: '150px'},
            { targets: [2], orderable: false, width: '150px'},
            { targets: [3], orderable: false, className: 'text-center', width: '150px'},
            { targets: [4], orderable: false, width: '150px'},
            { targets: [5], orderable: true, width: '150px'},
            { targets: [6], orderable: false, className: 'text-center', width: '100px'},
            { targets: [7], orderable: false, className: 'text-center', width: '150px'}
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
    if (type == "location") {
        return '<a href="javascript:void(0)" class="btn btn-success btn-location '+clazz+'" data-toggle="tooltip" title="備考" data-placement="top" data-id="'+id+'"><i class="fas fa-search fa-fw"></i></a>';
    }
    if (type == "map") {
        return '<a href="'+ link +'" target="_blank" class="btn btn-primary btn-map '+clazz+'" data-toggle="tooltip" title="Google Mapで表示" data-placement="top" data-id="'+id+'"><i class="fas fa-map-marked-alt"></i></a>';
    }
    if (type == "edit") {
        return '<a href="'+link+'" class="btn btn-primary '+clazz+'" data-toggle="tooltip" title="編集" data-placement="top"><i class="fas fa-edit fa-fw"></i></a>';
    }
    if (type == "remove") {
        return '<a href="javascript:void(0)" class="btn btn-danger btn-remove '+clazz+'" data-toggle="tooltip" title="削除" data-placement="top" data-id="'+id+'"><i class="fas fa-trash-alt fa-fw"></i></a>';
    }
    if (type == "remove_modal") {
        return '<a href="javascript:void(0)" class="btn btn-danger btn-remove-modal '+clazz+'" data-toggle="tooltip" title="削除" data-placement="top" data-id="'+id+'" data-url="'+ link +'"><i class="fas fa-trash-alt fa-fw"></i></a>';
    }
}