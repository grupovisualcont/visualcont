<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Moneda;

class Monedas extends BaseController
{
    public function index()
    {
        //
    }

    public function autoCompletado()
    {
        $busqueda = $this->request->getGet('search');
        $items = (new Moneda())->autoCompletado($busqueda);
        return $this->response->setJSON($items);
    }
}
