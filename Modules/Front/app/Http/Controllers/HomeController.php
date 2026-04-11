<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\BaseWebController;

class HomeController extends BaseWebController
{
    public function index()
    {
        return view('front::index');
    }

    public function search(Request $request)
    {

    }
}
