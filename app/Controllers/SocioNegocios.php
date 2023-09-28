<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SocioNegocio;

class SocioNegocios extends BaseController
{
    public function index()
    {
        //
    }

    public function autocompletado()
    {
        $busqueda = $this->request->getGet('search');
        $items = (new SocioNegocio())->autoCompletado($busqueda, 2, $this->request->getCookie('empresa'));
        return $this->response->setJson($items);
    }
}
