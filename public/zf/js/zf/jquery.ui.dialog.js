/**
 * jQuery-uiを使って簡単にダイアログを出すためのもの。
 * とりあえずConfirmとAlertのみ。
 *
 * 例)
 * var myDlg = new dialogJQUI();
 * //confirmサンプル
 * $('input[name=confirm]').click(function(){
 *      myDlg.dlgConfirm('confirm本文', 'ダイアログタイトル', function(){
 *          alert('ここがOKボタンで実行されます');
 *      });
 * });
 * //alertサンプル
 * $('input[name=alert]').click(function(){
 *      myDlg.dlgAlert('アラート本文', 'アラートタイトル');
 * });
 *
 * //confirmのオブジェクト渡しサンプル
 * //タイトル、本文、callbackの他、ボタン文字列やダイアログオプションも渡せる
 * $('input[name=confirmobj]').click(function(){
 *      myDlg.dlgConfirm({
 *          'title': 'タイトル',
 *          'body': '本文<br />のHTML',
 *          'callback': function(){
 *              alert('function for OK button');
 *          },
 *          'btnLabelOk': 'OKボタン文字列',
 *          'btnLabelCancel': 'キャンセルボタン文字列',
 *          'minWidth':600
 *      });
 * });
 *
 * //alertのオブジェクト渡しサンプル
 * //タイトル、本文の他、ボタン文字列やダイアログオプションも渡せる
 * $('input[name=alertobj]').click(function(){
 *      myDlg.dlgAlert({
 *          'title': 'タイトル',
 *          'body': '本文<br />のHTML',
 *          'btnLabelOk': 'OKボタン文字列',
 *          'minWidth':600
 *      });
 * });

 */
var dialogJQUI = function(btnLabels){

    var btnLabelOk = ' O K ';
    var btnLabelCancel = 'キャンセル';

    //ボタンラベルオプションの処理
    if (btnLabels && jQuery.type(btnLabels) == 'object') {
        if (btnLabels.ok) {
            btnLabelOk = btnLabels.ok;
        }
        if (btnLabels.cancel) {
            btnLabelCancel = btnLabels.cancel;
        }
    }

    //ダイアログ基本オプション
    var dlgOptions = {
            modal: true,
            closeOnEscape: false,
            draggable: false,
            resizable: false,
            minWidth: 400,
            maxWidth: 600,
            minHeight: 200,
            maxHeight: 300
    };


    /**
     * confirmを出す。callbackFuncにはfunction(){...}としてOK時の動作を書ける
     * キャンセル押したら何もしない。
     * 第一引数をオブジェクトとして、以下のように渡すことも可能。
     * この場合、ダイアログオプションもまとめて渡して変更可能。
     * dlgConfirm({
     *      'title':'ダイアログタイトル文字列',
     *      'body' :'ダイアログ本文文字列/HTML',
     *      'callback':function(){
     *          alert('function for OK button click');
     *      },
     *      'btnLabelOk': 'OKボタンラベル', //ボタン文字列変更可能
     *      'btnLabelCancel': 'キャンセルボタンラベル', //ボタン文字列変更可能
     *      'minWidth':500 //このようにjQuery.dialog用オプションも書ける
     * });
     */
    this.dlgConfirm = function(txtBody, txtTitle, callbackFunc) {
        //デフォルトボタンラベル設定
        var myBtnOk = btnLabelOk;
        var myBtnCancel = btnLabelCancel;
        
        //デフォルトダイアログオプション
        var myDlgOptions = dlgOptions;

        //ダイアログタイトル
        var myTitle = txtTitle;

        //ダイアログ本文
        var myBody = txtBody;

        //コールバック関数
        var myCallback = callbackFunc;


        //第一引数がオブジェクトの場合
        if (jQuery.type(txtBody) == 'object') {
            var myOpts = txtBody;
            //それぞれ上書き。第2、第3引数は無視
            if (myOpts.btnLabelOk) {
                myBtnOk = myOpts.btnLabelOk;
            }
            if (myOpts.btnLabelCancel) {
                myBtnCancel = myOpts.btnLabelCancel;
            }
            //ほかの引数は無視するので、body, title, callbackは単に上書き。
            myBody = myOpts.body;
            myTitle = myOpts.title;
            myCallback = myOpts.callback;
            //不要なものを整理してダイアログ用Optionとマージ
            delete myOpts.body;
            delete myOpts.title;
            delete myOpts.callback;
            delete myOpts.btnLabelOk;
            delete myOpts.btnLabelCancel;
            jQuery.extend(myDlgOptions, myOpts);
        }

        //タイトル設定
        myDlgOptions.title = myTitle;

        //ボタン
        var myButtons = [
            //OK Button
            {
                text: myBtnOk,
                click: function(){
                    $(this).dialog("close");
                    //OKボタン関数あったら実行
                    if (myCallback) {
                        myCallback();
                    }
                }
            },
            //cancel button
            {
                text: myBtnCancel,
                click: function(){
                    $(this).dialog("close");
                }
            }
        ];

        //ダイアログHTML
        var dlgHtml = $('<div />');
        //ダイアログ表示
        dlgHtml.html(myBody);
        dlgHtml.dialog(myDlgOptions);
        dlgHtml.dialog('option', 'buttons', myButtons);
    }


    /**
     * alertのようなOKボタンのみのダイアログを出す。
     * 引数は ダイアログ本文、ダイアログタイトル。
     * 第一引数をオブジェクトとして、以下のように渡すことも可能。
     * この場合、ダイアログオプションもまとめて渡して変更可能。
     * dlgConfirm({
     *      'title':'ダイアログタイトル文字列',
     *      'body' :'ダイアログ本文文字列/HTML',
     *      'btnLabelOk': 'OKボタンラベル', //ボタン文字列変更可能
     *      'minWidth':500      //このようにjQuery.dialog用オプションも書ける
     * });
     */
    this.dlgAlert = function(txtBody, txtTitle) {
        //デフォルトボタンラベル設定
        var myBtnOk = btnLabelOk;
        
        //デフォルトダイアログオプション
        var myDlgOptions = dlgOptions;

        //ダイアログタイトル
        var myTitle = txtTitle;

        //ダイアログ本文
        var myBody = txtBody;

        //第一引数がオブジェクトの場合
        if (jQuery.type(txtBody) == 'object') {
            var myOpts = txtBody;
            //それぞれ上書き。第2、第3引数は無視
            if (myOpts.btnLabelOk) {
                myBtnOk = myOpts.btnLabelOk;
            }
            //ほかの引数は無視するので、body, titleは単に上書き。
            myBody = myOpts.body;
            myTitle = myOpts.title;
            //不要なものを整理してダイアログ用Optionとマージ
            delete myOpts.body;
            delete myOpts.title;
            delete myOpts.btnLabelOk;
            jQuery.extend(myDlgOptions, myOpts);
        }

        //タイトル設定
        myDlgOptions.title = myTitle;

        //ボタン
        var myButtons = [
            //OK Button
            {
                text: myBtnOk,
                click: function(){
                    $(this).dialog("close");
                }
            }
        ];

        //ダイアログHTML
        var dlgHtml = $('<div />');
        //ダイアログ表示
        dlgHtml.html(myBody);
        dlgHtml.dialog(myDlgOptions);
        dlgHtml.dialog('option', 'buttons', myButtons);
    }

};
