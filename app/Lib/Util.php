<?php

namespace App\Lib;

class Util {

    /**
     * 指定日付を指定日時に加算し取得する
     * @param $date
     * @param $add
     * @param string $format(受け取りformat)
     * @return false|string
     */
    public static function getAddDayDate($date, $add, $format="Y-m-d H:i:s") {
        return date($format, strtotime($date." +$add day"));
    }
    /**
     * 指定日付を指定月に加算し取得する
     * @param $date
     * @param $add
     * @param string $format(受け取りformat)
     * @return false|string
     */
    public static function getAddMonthDate($date, $add, $format="Y-m-d H:i:s") {
        return date($format, strtotime($date." +$add month"));
    }
    /**
     * 指定時間を指定日時に加算し取得する
     * @param $date
     * @param $add
     * @param string $format
     * @return false|string
     */
    public static function getAddHourDate($date, $add, $format="Y-m-d H:i:s") {
        return date($format, strtotime($date." +$add hour"));
    }

    /**
     * 指定分を指定日時に加算し取得する
     * @param $date
     * @param $add
     * @param string $format
     * @return false|string
     */
    public static function getAddMinuteDate($date, $add, $format="Y-m-d H:i:s") {
        return date($format, strtotime($date." +$add min"));
    }
    /**
     * ２地点間の距離(m)を求める
     * ヒュベニの公式から求めるバージョン
     *
     * @param float $lat1 緯度１
     * @param float $lon1 経度１
     * @param float $lat2 緯度２
     * @param float $lon2 経度２
     * @param boolean $mode 測地系 true:世界(default) false:日本
     * @return float 距離(m)
     * ※参考URL
     * https://qiita.com/chiyoyo/items/b10bd3864f3ce5c56291
     *
     */
    public static function getDistance($lat1, $lon1, $lat2, $lon2, $mode=true) {
        // 緯度経度をラジアンに変換
        $radLat1 = deg2rad((float)$lat1); // 緯度１
        $radLon1 = deg2rad((float)$lon1); // 経度１
        $radLat2 = deg2rad((float)$lat2); // 緯度２
        $radLon2 = deg2rad((float)$lon2); // 経度２

        // 緯度差
        $radLatDiff = $radLat1 - $radLat2;

        // 経度差算
        $radLonDiff = $radLon1 - $radLon2;

        // 平均緯度
        $radLatAve = ($radLat1 + $radLat2) / 2.0;

        // 測地系による値の違い
        $a = $mode ? 6378137.0 : 6377397.155; // 赤道半径
        $b = $mode ? 6356752.314140356 : 6356078.963; // 極半径
        //$e2 = ($a*$a - $b*$b) / ($a*$a);
        $e2 = $mode ? 0.00669438002301188 : 0.00667436061028297; // 第一離心率^2
        //$a1e2 = $a * (1 - $e2);
        $a1e2 = $mode ? 6335439.32708317 : 6334832.10663254; // 赤道上の子午線曲率半径

        $sinLat = sin($radLatAve);
        $W2 = 1.0 - $e2 * ($sinLat*$sinLat);
        $M = $a1e2 / (sqrt($W2)*$W2); // 子午線曲率半径M
        $N = $a / sqrt($W2); // 卯酉線曲率半径

        $t1 = $M * $radLatDiff;
        $t2 = $N * cos($radLatAve) * $radLonDiff;
        $dist = sqrt(($t1*$t1) + ($t2*$t2));

        return $dist;
    }

    /**
     * 文字数チェック（$check_text = チェックしたい文字、$text_count = 制限文字数）
     * @param unknown $check_text
     * @param unknown $text_count
     * @return boolean
     */
    public static function text_count_check($check_text, $text_count){
        if(mb_strwidth($check_text,'UTF-8') > $text_count){
            return false;
        }
    }

    /**
     * 数値チェック（数値と.は可）（$check_number = チェックしたい数値）
     */
    public static function number_check($check_number){
        if(preg_match('/^([1-9]\d*|0)(\.\d+)?$/u', $check_number) !== 1){
            return false;
        }
    }
    /*
     * BR タグを改行コードに変換する
     */
    public static function br2nl($string)
    {
        // 大文字・小文字を区別しない
        $str = preg_replace('/<br[[:space:]]*\/?[[:space:]]*>/i', "\n", $string);
        // &nbsp;削除
        $str = html_entity_decode($str);
        $str = preg_replace("/\xC2\xA0/", "", $str);
        return $str;
    }
}
