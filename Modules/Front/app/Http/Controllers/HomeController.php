<?php

namespace Modules\Front\Http\Controllers;

use Modules\Base\Http\Controllers\BaseWebController;

class HomeController extends BaseWebController
{
    public function index()
    {
        return view('front::index');
    }
}
