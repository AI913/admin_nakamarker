$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

        // 一覧詳細ボタンクリック
        settingDetailAjax('/admin/user_location/detail/');

    }
});

/**
 * 一覧詳細(詳細ボタンがあるページは定義する)
 * @param data
 */
function setDetailView(data) {
    $('#detail_id').html(data.id);
    $('#detail_name').html(data.name);
    $('#detail_address').html(data.address);
    $('#detail_latitude_1').html(data.latitude_1);
    $('#detail_longitude_1').html(data.longitude_2);
    $('#detail_latitude_2').html(data.latitude_2);
    $('#detail_longitude_2').html(data.longitude_2);
    $('#detail_memo').html(replaceBR(data.memo_html));
    $('#detail_modal').modal('show');
}

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

    // DataTable設定
    settingDataTables(
        // 取得
    	'main_list',
        '/ajax/user-location',
        {
            'id': $('#id').val(),
            'name': $('#name').val(),
            'user_id': $('#user_id').val(),
        },
        // 各列ごとの表示定義
        [
            {data: 'id'},
            {
                // ロケーションイメージの画像を表示(モーダル形式)
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
            {data: 'latitude'},
            {data: 'longitude'},
            {data: 'user.name'},
            {data: 'marker.name'},
            {
                data: function (p) {
                    // 詳細・編集・削除
                    return getListLink('edit', 0, '/user-location/edit/'+p.id, 'list-button');
                        // getListLink('remove', p.id, '', 'list-button');
                }

            }
        ],
        // 各列ごとの装飾
        [
            { targets: [1], orderable: false, className: 'text-center', width: '150px'},
            { targets: [3], orderable: false, className: 'text-center', width: '100px'},
            { targets: [4], orderable: false, className: 'text-center', width: '100px'},
            { targets: [7], orderable: false, className: 'text-center', width: '100px'}
        ],
        search
    );
}

/**
 * 地図表示
 * @param id
 * @returns {*}
 */
function showMap(id) {
    // $('#map_modal').modal('show');
    // return;
    return $.ajax({
        url:    '/admin/ajax/user-location/detail',
        type:   'POST',
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data:   {'id': id}
    }).done(function(res){
        showZenrinMap(res.store.latitude_1, res.store.longitude_1, res.store.latitude_2, res.store.longitude_2, res.store.radius);
    });
}

