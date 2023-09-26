<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoVoucherCab;

class TipoVouchers extends BaseController
{

    public function index()
    {
        //
    }

    public function autoCompletado()
    {
        $resultado = (new TipoVoucherCab())->autoCompletado();
        return $this->response->setJSON($resultado);
    }

}
