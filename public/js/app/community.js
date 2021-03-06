$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

        // 一覧詳細ボタンクリック
        settingDetailAjax('/admin/community/detail/', '.btn-community');
    }

    // 公開フラグのvalue値設定
    $('#open_flg').change(function() {
        if($('#open_flg').prop('checked')) {
            $('#status').val(1);
        } else {
            $('#status').val(0);
        }
    })

    /*
     *   申請状況カラムのボタンが押下されたとき
     */
    $(document).on('click', '.btn-status', function(){
        // 申請状況の値を更新
        updateStatus($(this));
    });

    /*
     *   ユーザリストの"備考"ボタンが押下されたとき
     */
    $(document).on('click', '.btn-user',function(){
        let user_id = $(this).data('id');
        let community_id = $('#community_id').data('id');
        setUserDetail(user_id, community_id);
    });

    /*
     *   モーダルの終了処理
     */
        // 登録情報の画像
        $(document).on('click', '#location_image_close', function(){
            let id = $(this).data('id');
            $(`#location_modal${id}`).modal('hide');
        });
        $(document).on('click', '.close', function(){
            let id = $(this).data('id');
            $(`#location_modal${id}`).modal('hide');
        });
        // ユーザ情報の備考
        $(document).on('click', '#user_modal_close', function(){
            $('#community_user_modal').modal('hide');
        });

        // 2つ目のモーダルを閉じた後にbodyがスクロール出来る現象を防ぐため
        $('#community_user_modal').on('hidden.bs.modal', function () {
            if ($('.modal').is(':visible')) $('body').addClass('modal-open');
        });
});

/**
 * 申請状況の編集処理
 * @param {*} button
 */
function updateStatus(button) {
    $.ajax({
        url:    '/admin/ajax/community/history/update',
        type:   'POST',
        dataType: 'json',
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data:   {
            'user_id': $(button).data('user_id'),
            'community_id': $(button).data('community_id'),
            'status': $(button).data('status'),
        }
    }).done(function(response){
        $(button).data('status', response)
        if (response == 1) {
            $(button).removeClass('btn-danger');
            $(button).addClass('btn-info');
            $(button).html('申請中');
        } else if (response == 2) {
            $(button).removeClass('btn-info');
            $(button).addClass('btn-success');
            $(button).html('承認済み');
        } else if (response == 3) {
            $(button).removeClass('btn-success');
            $(button).addClass('btn-danger');
            $(button).html('却下');
        }
    })
}

/**
 * 会員情報表示
 * @param data
 */
function setDetailView(data, button) {

    // モーダルに表示する会員情報
    $('#detail_name').html(data.name);
    $('#detail_status').html(data.status_name);
    $('#community_id').data('id', data.id);

    if(button == '.btn-community') {
        if ($.fn.DataTable.isDataTable('#community_user_list')) {
            $('#community_user_list').DataTable().destroy();
        }

    /*
     *   "参加ユーザリスト"の表示
     */
        // DataTable設定
        settingDataTables(
            // 取得
            // tableのID
            'community_user_list',
            // 取得URLおよびパラメタ
            '/admin/ajax/community/detail/'+ data.id +'/user',
            {},
            // 各列ごとの表示定義
            [
                {data: 'history_id'},
                {data: 'name'},
                {data: 'email'},
                {data: 'updated_at_style', name: 'updated_at'},  // 参加日時はcommunity_historiesテーブルのstatusカラムが"承認済み"の場合のupdated_atカラムを参照
                {
                    data: function(p) {
                        // アカウントステータスが"アカウント停止"の場合は赤色で表示
                        if(p.status === 4) {
                            return ('<span style="color: red">'+ p.status_name +'</span>');
                        }
                        // それ以外は普通に表示
                        return p.status_name;
                    }, name: 'status'
                },
                {
                    data: function (p) {
                        // 申請中・承認済み・却下ボタンの設定
                        if(p.entry_status == 1) {
                            return '<button class="btn btn-info btn-status text-white w-75" data-user_id="'+ p.id +'" data-community_id="'+ p.community_id +'" data-status="'+ p.entry_status +'">'+ p.entry_status_name +'</button>';
                        }
                        if(p.entry_status == 2) {
                            return '<button class="btn btn-success btn-status text-white w-75" data-user_id="'+ p.id +'" data-community_id="'+ p.community_id +'" data-status="'+ p.entry_status +'">'+ p.entry_status_name +'</button>';
                        }
                        if(p.entry_status == 3) {
                            return '<button class="btn btn-danger btn-status text-white w-75" data-user_id="'+ p.id +'" data-community_id="'+ p.community_id +'" data-status="'+ p.entry_status +'">'+ p.entry_status_name +'</button>';
                        }
                    }, name: 'entry_status'
                },
                {
                    data: function (p) {
                        // '参加ユーザリスト'の備考ボタンの設定(備考はlocation_historiesのmemoカラムにデータがあるときのみ表示)
                        if(p.entry_memo == null) {
                            return '';
                        }
                        return getListLink('user', p.id, '', 'list-button');
                    }, name: 'entry_memo'
                },
            ],
            // 各列ごとの装飾
            [
                { targets: [4], orderable: true, className: 'text-center', width: '120px'},
                { targets: [5], orderable: true, className: 'text-center', width: '120px'},
                { targets: [6], orderable: false, className: 'text-center', width: '120px'},
            ],
            false
        );
        $('#community_modal').modal('show');
    }
}

/**
 * "参加ユーザリストの備考情報"モーダルの表示処理
 * @param id
 */
function setUserDetail(user_id, community_id) {
    // 削除フォームIDをセット
    $.ajax({url: `/admin/ajax/community/detail/${community_id}/user/${user_id}`})
    .done(function(response){
        if (response.status == 1) {
            $('#detail_user_memo').html(response.data.entry_memo);

            // 備考モーダルの表示
            $('#community_user_modal').modal('show');
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
    let outImage = 'images/noImage/no_image.png';

    $('#delete_flg').change(function() {
        // 画像の強制削除フラグ確認
        if($('#delete_flg').prop('checked') === true) {
            outImage = 'images/noImage/out_images.png';
            $('#delete_flg_on').val(true);
        }
        if($('#delete_flg').prop('checked') === false) {
            outImage = 'images/noImage/no_image.png';
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
        '/admin/ajax/community',
        {
            'id': $('#id').val(),
            'name': $('#name').val(),
            'type':   $('#type').val(),
            'status':   $('#status').val(),
        },
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
            {data: 'type_name', name: 'type'},
            {data: 'created_at_style', name: 'created_at'},
            {data: 'host_user_name'},
            {data: 'total_counts'},
            {
                data: function(p) {
                    // "非公開"の場合は赤色で表示
                    if(p.status === 0) {
                        return (`<span style='color: red'>${p.status_name}</span>`);
                    }
                    // "公開"の場合は青色で表示
                    return (`<span style='color: blue'>${p.status_name}</span>`);
                }, name: 'status'
            },
            {
                data: function (p) {
                    // "登録場所"ボタンの設定
                    return getListLink('location_list', p.id ,`/admin/community/detail/${p.id}/location`, 'list-button');
                }
            },
            // 各操作列
            {
                data: function (p) {
                    // 参加メンバー・編集ボタンの設定
                    return getListLink('community', p.id ,'/admin/community/detail/'+p.id, 'list-button') +
                           getListLink('edit', p.id, '/admin/community/edit/'+p.id, 'list-button') +
                           getListLink('remove', p.id ,'/admin/community/remove', 'list-button');
                }
            }
        ],
        // 各列ごとの装飾
        // 操作列(ボタン等)や画像項目はソート不可・text-centerを付与する
        [
            { targets: [1], orderable: false, className: 'text-center', width: '150px'},
            { targets: [2], orderable: true},
            { targets: [3], orderable: true, width: '120px'},
            { targets: [5], orderable: true, className: 'text-center', width: '100px'},
            { targets: [6], orderable: true, className: 'text-center', width: '100px'},
            { targets: [7], orderable: true, className: 'text-center', width: '100px'},
            { targets: [8], orderable: false, className: 'text-center', width: '100px'},
            { targets: [9], orderable: false, className: 'text-center', width: '150px'},
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
        return '<a href="javascript:void(0)" class="btn btn-warning text-white btn-community '+clazz+'" data-toggle="tooltip" title="参加ユーザ" data-placement="top" data-id="'+id+'"><i class="fas fa-users"></i></a>';
    }
    if (type == "user") {
        return '<a href="javascript:void(0)" class="btn btn-success btn-user '+clazz+'" data-toggle="tooltip" title="備考" data-placement="top" data-id="'+id+'"><i class="fas fa-search fa-fw"></i></a>';
    }
    if (type == "location") {
        return '<a href="javascript:void(0)" class="btn btn-success btn-location '+clazz+'" data-toggle="tooltip" title="備考" data-placement="top" data-id="'+id+'"><i class="fas fa-search fa-fw"></i></a>';
    }
    if (type == "location_list") {
        return '<a href="'+ link +'" class="btn btn-primary btn-location_list '+clazz+'" data-toggle="tooltip" title="登録場所" data-placement="top" data-id="'+id+'">登録場所</a>';
    }
    if (type == "map") {
        return '<a href="'+ link +'" target="_blank" class="btn btn-primary btn-map '+clazz+'" data-toggle="tooltip" title="Google Mapで表示" data-placement="top" data-id="'+id+'"><i class="fas fa-map-marked-alt"></i></a>';
    }
    if (type == "edit") {
        return '<a href="'+link+'" class="btn btn-primary '+clazz+'" data-toggle="tooltip" title="編集" data-placement="top"><i class="fas fa-edit fa-fw"></i></a>';
    }
    if (type == "edit_location") {
        return '<a href="javascript:void(0)" class="btn btn-primary '+clazz+'" data-toggle="tooltip" title="編集" data-placement="top"><i class="fas fa-edit fa-fw"></i></a>';
    }
    if (type == "remove") {
        return '<a href="javascript:void(0)" class="btn btn-danger btn-remove '+clazz+'" data-toggle="tooltip" title="削除" data-placement="top" data-id="'+id+'"><i class="fas fa-trash-alt fa-fw"></i></a>';
    }
}
