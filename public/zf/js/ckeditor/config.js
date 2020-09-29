/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	//config.removeButtons = 'Underline,Subscript,Superscript';
	config.removeButtons = '';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';


    // 追加の設定ここから
	config.language = 'ja';
    // //kcfinder呼び出し設定
	// //DocRootパスは呼び出し側でMyDocRootPathグローバル変数を定義することにする
    // //MyDocRootPathの最後に/が無かったら足す
    // var DocRoot = MyDocRootPath;
    // if (!DocRoot.match(/\/$/)) {
    //     DocRoot = DocRoot + '/';
    // }
	// config.filebrowserBrowseUrl      = DocRoot + 'zf/js/kcfinder/browse.php?type=files';
	// config.filebrowserImageBrowseUrl = DocRoot + 'zf/js/kcfinder/browse.php?type=images';
	// config.filebrowserFlashBrowseUrl = DocRoot + 'zf/js/kcfinder/browse.php?type=flash';
	// config.filebrowserUploadUrl      = DocRoot + 'zf/js/kcfinder/upload.php?type=files';
	// config.filebrowserImageUploadUrl = DocRoot + 'zf/js/kcfinder/upload.php?type=images';
	// config.filebrowserFlashUploadUrl = DocRoot + 'zf/js/kcfinder/upload.php?type=flash';
    //
	// // KCFinderUserあったらそれを足すようにする
	// if ("KCFinderUser" in window) {
	// 	config.filebrowserBrowseUrl      += '&user=' + KCFinderUser;
	// 	config.filebrowserImageBrowseUrl += '&user=' + KCFinderUser;
	// 	config.filebrowserFlashBrowseUrl += '&user=' + KCFinderUser;
	// 	config.filebrowserUploadUrl      += '&user=' + KCFinderUser;
	// 	config.filebrowserImageUploadUrl += '&user=' + KCFinderUser;
	// 	config.filebrowserFlashUploadUrl += '&user=' + KCFinderUser;
	// }
    //
	// // KCFinderMyPathあったらそれを足すようにする
	// if ("KCFinderMyPath" in window) {
	// 	config.filebrowserBrowseUrl      += '&mypath=' + KCFinderMyPath;
	// 	config.filebrowserImageBrowseUrl += '&mypath=' + KCFinderMyPath;
	// 	config.filebrowserFlashBrowseUrl += '&mypath=' + KCFinderMyPath;
	// 	config.filebrowserUploadUrl      += '&mypath=' + KCFinderMyPath;
	// 	config.filebrowserImageUploadUrl += '&mypath=' + KCFinderMyPath;
	// 	config.filebrowserFlashUploadUrl += '&mypath=' + KCFinderMyPath;
	// }


    //タグにclass等の属性添付を許可する場合
    //config.allowedContent = true;

};

CKEDITOR.on( 'dialogDefinition', function( ev ) {
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;

    if ( dialogName == 'table' ) {
        var info = dialogDefinition.getContents( 'info' );

        info.get( 'txtWidth' )[ 'default' ] = '';       // Set default width to null
    }
});
