<?php
namespace App\Lib;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * 共通処理クラス
 * Class Common
 */
class Common {

    /**
     * 指定日時の曜日を取得
     * @param $date
     * @return mixed|string
     */
    public static function getWeekFromDate($date) {
        if (!$date) {
            return "";
        }
        $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
        ];

        return $week[date('w', strtotime($date))];
    }

    /**
     * 指定した日付の曜日込み年月日取得
     * @param $date
     * @param string $format
     * @return string
     */
    public static function getYmdWeekDate($date, $format="Y年m月d日") {
        if (!$date) {
            return date($format). " (".self::getWeekFromDate(date('w')).")";
        }
        return date($format, strtotime($date)) . " (".self::getWeekFromDate(date('w', strtotime($date))).")";
    }
    /**
     * ファイル名から拡張子取得(.付き)
     * @param $name
     * @return bool|string
     */
    public static function getExt($name) {
        return substr($name, strrpos($name, '.'));
    }
    /**
     * 画像URL取得
     * @param $image_file
     * @return string
     */
    public static function getImageUrl($image_file, $folder) {
        if ($image_file === null) {
            return asset('images/noImage/no_image.png');
        }
        if ($image_file === config('const.out_image')) {
            return asset('images/noImage/out_images.png');
        }
        return Storage::url("images/".$folder.'/'.$image_file);
    }
    /**
     * ファイル保存
     * @param $file
     * @return string
     * \Image : intervention/imageパッケージ
     * storage/app/public/imagesフォルダに保存
     */
    public static function saveImage($file, $folder) {
        $tmp_name   = md5(microtime());                    // フィル名取得(microtime() : Unixタイムスタンプ)
        $ext        = $file->getClientOriginalExtension(); // 拡張子GET
        $image_name = $tmp_name.".".$ext;

        // $fileのオープン
        $image = \Image::make($file);
        // 小サイズ
        $image->fit(config('const.resize_width'), config('const.resize_height'), function ($constraint) {
            $constraint->aspectRatio(); // リサイズの決まり文句
        });
        Storage::put("public/images/".$folder."/".$image_name, $image, $image->encode());

        return $image_name;
    }
    /**
     * ファイル削除
     * @param $file
     */
    public static function removeImage($file, $folder) {
        // $ext    = self::getExt($file);
        Storage::delete("public/images/".$folder."/".$file);
        // Storage::delete("images/".str_replace($ext, "-s".$ext, $file));
        // Storage::delete("images/".str_replace($ext, "-m".$ext, $file));
        return;
    }

    /**
     * ユーザーステータスリスト
     * @return array
     */
    public static function getUserStatusList() {
        // 管理者ステータス以外のリスト
        $status = [
            ['id' => config('const.user_app_member'),      'name' => config('const.user_app_member_name')],
            ['id' => config('const.user_app_unsubscribe'), 'name' => config('const.user_app_unsubscribe_name')],
            ['id' => config('const.user_admin_system'),    'name' => config('const.user_admin_system_name')],
            ['id' => config('const.user_app_account_stop'),    'name' => config('const.user_app_account_stop_name')],
        ];
        return $status;
    }

    /**
     * マーカー種別リスト
     * @return number[][]|string[][]
     */
    public static function getMarkerTypeList() {
        return [
            [
                'id' => config('const.marker_type_register'), 
                'name' => config('const.marker_type_register_name')
            ],
            [
                'id' => config('const.marker_type_function'), 
                'name' => config('const.marker_type_function_name')
            ],
            [
                'id' => config('const.marker_type_search'), 
                'name' => config('const.marker_type_search_name')
            ],
        ];
    }

    /**
     * コミュニティ種別リスト
     * @return number[][]|string[][]
     */
    public static function getCommunityTypeList() {
        return [
            [
                'id' => config('const.community_official'), 
                'name' => config('const.community_official_name')
            ],
            [
                'id' => config('const.community_official_free'), 
                'name' => config('const.community_official_free_name')
            ],
            [
                'id' => config('const.community_personal'), 
                'name' => config('const.community_personal_name')
            ],
            [
                'id' => config('const.community_personal_open'), 
                'name' => config('const.community_personal_open_name')
            ],
        ];
    }

    /**
     * 公開種別リスト
     * @return array
     */
    public static function getOpenStatusList() {
        return [
            ['id' => config('const.private'),     'name' => config('const.private_name')],
            ['id' => config('const.open'),     'name' => config('const.open_name')],
        ];
    }

    /**
     * 申請種別リスト
     * @return array
     */
    public static function getEntryStatusList() {
        return [
            ['id' => config('const.community_history_apply'),     'name' => config('const.community_history_apply_name')],
            ['id' => config('const.community_history_approval'),   'name' => config('const.community_history_approval_name')],
            ['id' => config('const.community_history_reject'),     'name' => config('const.community_history_reject_name')],
        ];
    }

    /**
     * ポイント種別リスト
     * @return array
     */
    public static function getPointStatusList() {
        return [
            ['id' => config('const.point_buy'),       'name' => config('const.point_buy_name')],
            ['id' => config('const.point_gift'),      'name' => config('const.point_gift_name')],
            ['id' => config('const.point_advertise'), 'name' => config('const.point_advertise_name')],
            ['id' => config('const.point_admin'),     'name' => config('const.point_admin_name')],
        ];
    }

    /**
     * ポイント有料フラグリスト
     * @return array
     */
    public static function getPointChargeFlagList() {
        return [
            ['id' => config('const.charge_flg_off'),     'name' => config('const.charge_flg_off_name')],
            ['id' => config('const.charge_flg_on'),      'name' => config('const.charge_flg_on_name')],
        ];
    }

    /**
     * お知らせ種別リスト
     * @return number[][]|string[][]
     */
    public static function getNewsTypeList() {

        return [
            ['id' => config('const.official_type'), 'name' => config('const.official_type_name')],
            ['id' => config('const.community_type'), 'name' => config('const.community_type_name')],
        ];
    }
    /**
     * お知らせプッシュ通知種別リスト
     * @return number[][]|string[][]
     */
    public static function getInformationPushList() {

        return [
            ['id' => 0, 'name' => '通知しない'],
            ['id' => 1, 'name' => '通知する'],
        ];
    }
    
    /**
     * 指定パスワードの暗号化取得
     * @param $password
     * @return string
     */
   	public static function getEncryptionPassword($password) {
   	    // bcrypt暗号化
   	    return bcrypt($password);
    }
}
