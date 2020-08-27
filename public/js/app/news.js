$(function(){
    // 一覧画面のみ適用(ID=main_listがある場合のみ)
    if ($('#main_list').length) {

        // DataTables初期化
        initList(false);

        // 一覧詳細ボタンクリック
        settingDetailAjax('/news/detail/');
    }
});

/**
* 一覧詳細(詳細ボタンがあるページは定義する)
* @param data
*/
function setDetailView(data) {
    $('#detail_title').html(data.title);
    $('#detail_status').html(data.status_name);
    $('#detail_image_file').attr('src', data.image_url);
    $('#detail_body').html(replaceBR(data.body));
    $('#detail_open_date').html(data.condition_start_time);
    $('#detail_modal').modal('show');
}

/**
 * 画面固有チェック
 * @returns {boolean}
 */
function customCheck() {
    let check = true;

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
        '/ajax/news',
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
            {data: 'condition_start_time'},
            {data: 'condition_end_time'},
            {data: 'type_name'},
            {data: 'user.name'},
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
            { targets: [3], orderable: false, className: 'text-center', width: '100px'},
            { targets: [6], orderable: false, className: 'text-center', width: '110px'},
            { targets: [8], orderable: false, className: 'text-center', width: '150px'}
           ],
           search
    );
}
