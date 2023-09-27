<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoActivo extends Model
{
    protected $table = 'tipoactivo';

    protected $primaryKey = 'codTipoActivo';

    protected $allowedFields = [
        'codTipoActivo',
        'CodEmpresa',
        'descTipoActivo'
    ];
}
