<?php

namespace App\Models;

use CodeIgniter\Model;

class CondicionPago extends Model
{
    protected $table = 'condicionpago';

    protected $primaryKey = 'codcondpago';

    protected $allowedFields = [
        'codcondpago',
        'CodEmpresa',
        'desccondpago',
        'comentario',
        'con_cre',
        'Ndias',
        'carga_inicial',
        'Tipo',
        'Estado'
    ];
}
