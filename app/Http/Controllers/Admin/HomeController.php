<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class HomeController extends BaseAdminController
{
    // メインルート
    protected $mainRoot;

    /**
     * コンストラクタ
     * @return void
     */
    public function __construct()
    {
        $this->mainRoot = "admin/home";
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view($this->mainRoot."/index");
    }
}
