<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\ConfigService;

class ConfigController extends BaseAdminController
{
    /**
     * システム管理コントローラー
     * Class ConfigController
     * @package App\Http\Controllers
     */
    public function __construct(ConfigService $mainService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/config";
        $this->mainTitle    = '共通設定管理';
    }

}
