<?php
namespace App\Services\View;
use Illuminate\Support\ServiceProvider;

/**
 * View側共通サービス
 * Class ViewService
 * @package App\Services
 */
class ViewService extends ServiceProvider {

    /**
     * 管理メニューアクティブ判定
     * @param $path
     * @return string
     */
    public static function isMenuActive($path) {
        if (request()->is('*'.$path.'*')) {
            return ' active';
        }
        return '';
    }
    /**
     * 管理メニューOpen判定
     * @param $path
     * @return string
     */
    public static function isMenuOpen($path) {
        if (request()->is('*'.$path.'*')) {
            return ' open';
        }
        return '';
    }
}