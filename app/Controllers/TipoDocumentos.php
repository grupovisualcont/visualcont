<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoDocumento;

class TipoDocumentos extends BaseController
{
    public function autoCompletado()
    {
        $resultado = (new TipoDocumento())->autoCompletado();
        return response($resultado)->getJSON();
    }
}
