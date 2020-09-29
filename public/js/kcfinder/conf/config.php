<?php

/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 3.12
  *    @author Pavel Tzonkov <sunhater@sunhater.com>
  * @copyright 2010-2014 KCFinder Project
  *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
  *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
  *      @link http://kcfinder.sunhater.com
  */

/* IMPORTANT!!! Do not comment or remove uncommented settings in this file
   even if you are using session configuration.
   See http://kcfinder.sunhater.com/install for setting descriptions */

require($_SERVER['DOCUMENT_ROOT'].'/../vendor/autoload.php');
require($_SERVER['DOCUMENT_ROOT'].'/../bootstrap/app.php');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class)
    ->pushMiddleware(\App\Http\Middleware\EncryptCookies::class)
    ->pushMiddleware(\Illuminate\Session\Middleware\StartSession::class)
    ->handle(Illuminate\Http\Request::capture());

/**
 * ログイン中かどうか判定する
 * @return bool
 */
function CheckAuthentication(){
    return auth()->guard('admin')->check() ? false : true;
}


// ★アップロード先と、画像ブラウザのURLを設定
define('KCF_UPLOAD_URL', "/kcfimage/news");
define('KCF_UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT']."/kcfimage/news");
//'uploadURL' => KCF_UPLOAD_URL,
//'uploadDir' => KCF_UPLOAD_DIR,

// 階層が無い場合作成
if (!is_dir($_SERVER['DOCUMENT_ROOT']."/kcfimage/news")) {
    mkdir($_SERVER['DOCUMENT_ROOT']."/kcfimage/news", 0777, true);
    mkdir($_SERVER['DOCUMENT_ROOT']."/kcfimage/news/images", 0777, true);
    mkdir($_SERVER['DOCUMENT_ROOT']."/kcfimage/news/files", 0777, true);
}


$mypath = '';
if (!empty($_GET['type'])) {
    $mypath = "/" . trim($_GET['type']);
}

//umaskをクリアしておかないとkcfinderの設定パーミッションが効かないので
umask(0000);

$_CONFIG = array(


// GENERAL SETTINGS
//@masahi6
    'disabled' => CheckAuthentication(),
    'uploadURL' => KCF_UPLOAD_URL,
    'uploadDir' => KCF_UPLOAD_DIR,
    'theme' => "default",

    'types' => array(

    // (F)CKEditor types
        'files'   =>  "",
        'flash'   =>  "swf",
        'images'  =>  "*img",
        'medias'   =>  "swf flv avi mpg mpeg qt mov wmv asf rm mp4",

    // TinyMCE types
        'file'    =>  "",
        'media'   =>  "swf flv avi mpg mpeg qt mov wmv asf rm",
        'image'   =>  "*img",
    ),


// IMAGE SETTINGS

    'imageDriversPriority' => "imagick gmagick gd",
    'jpegQuality' => 90,
    'thumbsDir' => ".thumbs",

    'maxImageWidth' => 0,
    'maxImageHeight' => 0,

    'thumbWidth' => 100,
    'thumbHeight' => 100,

    'watermark' => "",


// DISABLE / ENABLE SETTINGS

    'denyZipDownload' => false,
    'denyUpdateCheck' => false,
    'denyExtensionRename' => false,


// PERMISSION SETTINGS
//@masahi6
    'dirPerms' => 0777,
    'filePerms' => 0666,

    'access' => array(

        'files' => array(
            'upload' => true,
            'delete' => true,
            'copy'   => true,
            'move'   => true,
            'rename' => true
        ),

        'dirs' => array(
            'create' => true,
            'delete' => true,
            'rename' => true
        )
    ),

    'deniedExts' => "exe com msi bat cgi pl php phps phtml php3 php4 php5 php6 py pyc pyo pcgi pcgi3 pcgi4 pcgi5 pchi6",


// MISC SETTINGS

    'filenameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'dirnameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'mime_magic' => "",

    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',


// THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION SETTINGS

    '_normalizeFilenames' => false,
    '_check4htaccess' => true,
    //'_tinyMCEPath' => "/tiny_mce",

    '_sessionVar' => "KCFINDER",
    //'_sessionLifetime' => 30,
    //'_sessionDir' => "/full/directory/path",
    //'_sessionDomain' => ".mysite.com",
    //'_sessionPath' => "/my/path",

    //'_cssMinCmd' => "java -jar /path/to/yuicompressor.jar --type css {file}",
    //'_jsMinCmd' => "java -jar /path/to/yuicompressor.jar --type js {file}",

);

?>
