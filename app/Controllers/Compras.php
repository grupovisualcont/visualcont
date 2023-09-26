<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Compras extends BaseController
{
    public function index()
    {
        //
    }

    public function crear()
    {

        return viewApp('Compra', 'app/compra/crear');
    }
}
