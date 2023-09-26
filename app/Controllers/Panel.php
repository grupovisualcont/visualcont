<?php

namespace App\Controllers;

use App\Controllers\BaseController;

session_start();

class Panel extends BaseController
{
    public function index()
    {
        return view('app/panel/index');
    }
}
