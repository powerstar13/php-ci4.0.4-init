<?php namespace Modules\Common\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
    /**
     * 홈 페이지
     *
     * @return View
     */
    public function index()
    {
        return view('welcome_message');
    }
}