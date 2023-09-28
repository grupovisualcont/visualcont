<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CondicionPago;

class CondicionesPago extends BaseController
{
    public function index()
    {
        //
    }

    public function autoCompletado()
    {
        $busqueda = $this->request->getGet('search');
        $items = (new CondicionPago())->autoCompletado($busqueda, $this->request->getCookie('empresa'));
        return $this->response->setJSON($items);
    }
}
