/**
 * 住所文字列からGoogleGeocoderで緯度経度取得して
 * Zf_xxx_openGeoWin(lat,lng)を叩くクラス。
 * xxxの部分は引数のformNameになる。
 * Hqf_View_Helper_Gmapと連携。
 * 
 * 駆動用ボタンのnameとして'get_' + formName + '_btn'を使用する。
 */
var Zf_GetAddrMap = function(formName) {

    var myObj = this;

    //動作のターゲットとするボタン要素
    this.targetBtnElem = jQuery('[name="get_' + formName + '_btn"]');
    //バルーンID
    this.balloonId = 'zf_GAM_' + formName + '_balloon';
    this.formName = formName;

    this.GetMap = function(addrStr, balloonOption) {
        //バルーンオプションデフォルト
        var balloonPos = 'left';
        var balloonCss = {
            'min-width' : '400px',
            'max-width' : '500px',
            'max-height': '250px',
            'line-height' : '1.8em',
            'opacity' : "1",
            'word-wrap' : 'break-word'
        };
        //バルーンオプションある場合は上書き
        if (balloonOption) {
            if (balloonOption.position) {
                balloonPos = balloonOption.position;
            }
            if (balloonOption.css) {
                balloonCss = balloonOption.css;
            }
        }

        //geocoderから取得
        var myGeoCoder = new google.maps.Geocoder();
        myGeoCoder.geocode({address: addrStr}, function(results, status) {
            //ステータスがOKでなければ終了
            if (status != google.maps.GeocoderStatus.OK) {
                return;
            }

            //取得が1個ならそれ使って終了
            if (results.length == 1) {
                var latlng;
                if (results[0].geometry) {
                    latlng = results[0].geometry.location;
                    //取得できてたら代入して別関数蹴って終了
                    if (latlng) {
                        //Mapスクリプト蹴る
                        return myObj.kickMapScript(latlng.lat(), latlng.lng());
                    }
                }
            } else {
                //複数回答の場合はバルーン出して選ばせる。
                var locateList = Array();
                //リスト作成
                for (var i in results) {
                    var obj = results[i];
                    var tmp = {
                        'addr': obj.formatted_address,
                        'lat' : obj.geometry.location.lat(),
                        'lng' : obj.geometry.location.lng()
                    };
                    locateList[i] = tmp;
                }
                var listHtml = myObj.makeGeoBalloonHtml(locateList);
                //ボタン要素に対してバルーンを出す
                myObj.targetBtnElem.showBalloon({
                    contents : listHtml, 
                    position : balloonPos,
                    css      : balloonCss
                });
                //バルーン内name=bln_closeなAタグに対して閉じる動作
                jQuery('#' + myObj.balloonId).find('a[name=bln_close]').bind('click', function(){
                    myObj.closeBalloon();
                    return false;
                });
                //バルーン内リストAに対して地図開く動作
                jQuery('#' + myObj.balloonId).find('.balloon_item_list').each(function(){
                        //値の取得
                        var tmpLat = jQuery(this).find('span[name=latTxt]').text();
                        var tmpLng = jQuery(this).find('span[name=lngTxt]').text();
                        var tmpAddr = jQuery(this).find('span[name=addrTxt]').text();
                        //onClickでMap開いてバルーンを閉じる
                        jQuery(this).find('a').bind('click', function(){
                            myObj.kickMapScript(tmpLat, tmpLng);
                            myObj.closeBalloon();
                            return false;
                        });
                    });
            }
        });
    }

    //Mapスクリプト蹴る
    this.kickMapScript = function(lat, lng) {
        var mapScript = "return Zf_" + this.formName + "_openGeoWin(addrLat, addrLng)";
        var mapFunc = new Function('addrLat', 'addrLng', mapScript);
        return ret = mapFunc(lat, lng);
    }

    this.closeBalloon = function() {
        this.targetBtnElem.hideBalloon();
        return false;
    }


    this.makeGeoBalloonHtml = function(list) {
        //外枠
        var oHtml = jQuery('<div></div>');
        oHtml.attr('id', this.balloonId);
        //閉じるボタン枠
        var closeHtml = jQuery('<div></div>');
        closeHtml.css({ 'width':'8em', 'position':'absolute', 'right':'2px', 'top':'5px', 'text-align':'right'});
        //閉じるボタン
        var closeBtn = jQuery('<a></a>');
        closeBtn.attr('href', '#').attr('name', 'bln_close').text('[X] 閉じる');
        closeBtn.css({'color':'#000', 'font-weight':'bold'});
        closeHtml.append(closeBtn);
        oHtml.append(closeHtml);
        //候補数表示
        var numHtml = jQuery('<p></p>');
        numHtml.html('候補 ' + list.length + '件');
        numHtml.css({'color':'#000'});
        oHtml.append(numHtml);

        var listHtml = jQuery('<ul></ul>');
        listHtml.css({'max-height':'180px', 'overflow':'auto', 'overflow-y':'scroll'});
        for (var i in list) {
            var obj = list[i];
            var tmpHtml = jQuery('<li></li>');
            tmpHtml.attr('class', 'balloon_item_list').css('list-style', 'none');
            var in_a = jQuery('<a></a>');
            in_a.attr('href', '#');
            //text
            var in_addr = jQuery('<span></span>');
            in_addr.attr('name', 'addrTxt').text(obj.addr);
            in_a.append(in_addr);
            //lat
            var in_lat = jQuery('<span></span>');
            in_lat.attr('name', 'latTxt').css('display', 'none').text(obj.lat);
            in_a.append(in_lat);
            //lng
            var in_lng = jQuery('<span></span>');
            in_lng.attr('name', 'lngTxt').css('display', 'none').text(obj.lng);
            in_a.append(in_lng);
            tmpHtml.append(in_a);
            listHtml.append(tmpHtml);
        }
        oHtml.append(listHtml);

        return oHtml;
    }
}

