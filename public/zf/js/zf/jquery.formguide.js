/**
 * フォームに案内文字列を表示させる
 * Submit時に未入力なら案内文字列を消去してから送信
 */
var FormGuide = function(formname, userOptions){

    //init text color
    var textColor  = '#BBB';
    var textBG     = '#FFF';
    //input textcolor
    var inputColor = '#000';
    var inputBG    = '#FFF';


    var inputList = {};
    var textareaList = {};

    if (userOptions) {
        if (userOptions.textColor) { textColor = userOptions.textColor;}
        if (userOptions.inputColor) { inputColor = userOptions.inputColor;}
        if (userOptions.textBG) { textBG = userOptions.textBG;}
        if (userOptions.inputBG) { inputBG = userOptions.inputBG;}
    }


    this.setText = function(elemname, initText) {
        //get
        var fElem = jQuery("input[name="+elemname+"]");
        //set
        setGuide(fElem, initText);
        //list
        inputList[elemname] = initText;
    }

    this.setTextarea = function(elemname, initText) {
        //get
        var fElem = jQuery("textarea[name="+elemname+"]");
        //set
        setGuide(fElem, initText);
        //list
        textareaList[elemname] = initText;
    }


    /**
     * Selectは色変更のみ
     */
    this.setSelect = function(elemname) {
        //get
        var fElem = jQuery("select[name="+elemname+"]");
        //init
        if (!fElem.val()) {
            fElem.css("color", textColor).css("background-color", textBG);
        }
        fElem.focus(function(){
            jQuery(this).css("color", inputColor).css("background-color", inputBG);
        });
        fElem.blur(function(){
            if (!jQuery(this).val()) {
                jQuery(this).css("color", textColor).css("background-color", textBG);
            }
        });
    }



    function setGuide(fElem, initText) {
        //init
        if (fElem.val() == "" || fElem.val() == initText) {
            fElem.val(initText)
                .css("color", textColor)
                .css("background-color", textBG);
        }
        //onfocus
        fElem.focus(function(){
            if (this.value == initText) {
                jQuery(this).val("")
                    .css("color", inputColor)
                    .css("background-color", inputBG);
            }
        });
        //blur
        fElem.blur(function(){
            if (this.value == "" || this.value == initText) {
                jQuery(this).val(initText)
                    .css("color", textColor)
                    .css("background-color", textBG);
            }
        });
    }


	//以下、formnameのsubmit動作に対してフィルタを掛ける
	var formObj = jQuery("form[name="+formname+"]");
	//onsubmit
	formObj.submit(function(){
		//check
		for (var elemname in inputList) {
            var elem = jQuery("input[name="+elemname+"]");
            var initText = inputList[elemname];
            if (elem.val() == initText) {
                elem.val("");
            }
        }
		//check textarea
		for (var elemname in textareaList) {
			var elem = jQuery("textarea[name="+elemname+"]");
            var initText = textareaList[elemname];
            if (elem.val() == initText) {
                elem.val("");
            }
        }
    });
}
