<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Model\NewsService;

class NewsController extends BaseAdminController
{
    /**
     * 情報管理コントローラー
     * Class NewsController
     * @package App\Http\Controllers
     */
    public function __construct(NewsService $mainService) 
    {
        parent::__construct();
        $this->mainService  = $mainService;
        $this->mainRoot     = "admin/news";
        $this->mainTitle    = 'お知らせ管理';
    }
    public function index() {
        return view($this->mainRoot.'/list');
    }

}
