<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\PushHistoryService;

class PushController extends BaseAdminController
{
    /**
     * 通知機能管理コントローラー
     * Class PushController
     * @package App\Http\Controllers
     */
    public function __construct(PushHistoryService $mainService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/push";
        $this->mainTitle    = 'プッシュ通知履歴管理';
    }

}
