/**
 * Tag入力ヘルパーバルーン
 */
//ここからタグ用バルーンクラス
var TagBalloon = function(targetForm, tagList, tagDelim) {
    //ターゲットフォーム要素をセット
    this.targetForm = $(targetForm);
    this.tagList = tagList;
    //ターゲット要素のinput_nameからballoonクラス名を作成
    var targetName = this.targetForm.attr('name');
    this.blnClsName = "tagTipBln_" + targetName;
    this.blnSearchName = "tgsrc_" + targetName;
    this.setBalloon();


    //1ページあたりのタグ数
    this.tagsPerPage = 10;
    //カレント
    this.tagsPageCurrent = 1;
    //総ページ数
    this.tagsAllPage = Math.ceil(this.tagList.length / this.tagsPerPage);

};

//バルーンセット
TagBalloon.prototype.setBalloon = function() {
    //リストなければ何もしない
    if (this.tagList.length <= 0) {
        return;
    }

    var tagHtml = this.makeBalloonHtml();
    var myObj = this;
    this.targetForm.bind('focus', function(){
        $(this).showBalloon({
            contents: tagHtml,
            position: 'bottom'
            ,css: {
                'width': '350px',
                'min-height' : '1em',
                'word-wrap': 'break-word',
                'font-size': 'small',
                'line-height': '1.5em',
                'opacity' : '0.95'
            }
        });
        //バルーン内のAタグにイベントを仕込む
        //バルーン表示させてからでないと出来ないのでここでやる
        $('.' + myObj.blnClsName + ' > span > a').bind('click', function(){
            var tmpTxt = $(this).text();
            myObj.setTag($(this));
            return false;
        });
        //選択中のタグを太字にする
        var curTags = myObj.targetForm.val();
        if (curTags) {
            $('.' + myObj.blnClsName + ' > span > a').each(function(){
                var myTxt = $(this).text();
                var reg = new RegExp(','+myTxt+',|,'+myTxt+'$|^'+myTxt+',|^'+myTxt+'$');
                if (curTags.match(reg)) {
                    $(this).css('font-weight', 'bold');
                }
            });
        }


        //バルーンのinputにイベント仕込む
        $('input[name=' + myObj.blnSearchName + ']').bind('keyup', function(){
            myObj.searchTag();
        });
        //バルーンのcloseにイベント
        $('.' + myObj.blnClsName).find('a[name=close]').bind('click', function(){
            myObj.targetForm.hideBalloon();
        });


        //alert(myObj.targetForm.parents().find('form').attr('name'));

    });
    /*
    this.targetForm.bind('blur', function(){
        $(this).hideBalloon();
    });
    */

};


//onKeyUpで絞り込む機能
TagBalloon.prototype.searchTag = function(){
    /*
    var formVal = this.targetForm.val().split(',');
    var searchKey = formVal.pop();
    */
    var searchKey = $('input[name=' + this.blnSearchName + ']').val();

    $('.tagBlnDiv').find('span').each(function(){
        var text = $(this).text();
        if ($(this).text().indexOf(searchKey) === -1) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });
};


//tooltip用HTML作成
TagBalloon.prototype.makeBalloonHtml = function() {
    var tgBlnHtml = '<div class="' + this.blnClsName + ' tagBlnDiv">';
    tgBlnHtml += '<div style="text-align:right;margin-bottom:3px;"><input type="text" name="' + this.blnSearchName +'" size="20" />';
    tgBlnHtml += '&nbsp;<a href="#" name="close" style="color:#000;font-weight:bold;">[X]</a></div>'
    for (i = 0; i < this.tagList.length; i++) {
        //ちょっと枠はみ出しちゃうのでnowrapはやめとく
        tgBlnHtml += '<span style="white-space:nowrap;margin-right:5px;">[<a href="#">';
        //tgBlnHtml += '<span>[<a href="#">';
        tgBlnHtml += this.tagList[i];
        tgBlnHtml += '</a>]</span>  ';
    }
    tgBlnHtml += '</div>';
    return tgBlnHtml;
};

//重複を取り除く関数
TagBalloon.prototype.array_unique = function(array) {
    var storage = {};
    var uniqueArray = [];
    var i,value;
    for ( i=0; i<array.length; i++) {
        value = array[i];
        //空欄と重複でなければ追加
        if (value && !(value in storage)) {
            storage[value] = true;
            uniqueArray.push(value);
        }
    }
    return uniqueArray;
};
//array_search関数//phpと同じく存在する場合にキーを返す
TagBalloon.prototype.array_search = function(val, array) {
    var ar_len = array.length;
    for (i = 0; i < ar_len; i++) {
        if (array[i] == val) {
            return i;
        }
    }
    return false;
};

TagBalloon.prototype.setTag = function(elem) {
    //入力タグを取得してtrimしておく
    var tagTxt = elem.text();
    tagTxt = tagTxt.replace(/(^\s+)|(\s+$)/g, '');

    //現在のタグ文字列を配列化
    var curTags = this.targetForm.val().split(',');
    for (i = 0; i < curTags.length; i++) {
        //タグをtrimしておく
        curTags[i] = curTags[i].replace(/(^\s+)|(\s+$)/g, '');
    }

    //curTagsになければタグ追加、あったら削除
    var existKey = this.array_search(tagTxt, curTags);
    if (existKey === false) {
        //タグ追加
        curTags.push(tagTxt);
    } else {
        //削除する
        curTags.splice(existKey, 1);
    }

    //重複整理
    newTags = this.array_unique(curTags);
    //デリミタで繋ぎなおす
    newTags = newTags.join(',');
    //書き戻す
    this.targetForm.val(newTags);
    //フォーカスをtargetに戻す
    this.targetForm.focus();
};

