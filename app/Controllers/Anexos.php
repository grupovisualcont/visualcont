<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;

class Anexos extends BaseController
{
    public function index()
    {
        //
    }

    public function autoCompletadoTipoOperacion()
    {
        $busqueda = $this->request->getGet('search');
        $items = (new Anexo())->autoCompletado($busqueda, $this->request->getCookie('empresa'), '4');
        return $this->response->setJSON($items);
    }
}
