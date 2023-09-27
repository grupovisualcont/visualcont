<?php

namespace App\Models;

use CodeIgniter\Model;

class CentroCosto extends Model
{
    protected $table = 'centrocosto';

    protected $primaryKey = 'CodcCosto';

    protected $allowedFields = [
        'CodcCosto',
        'CodEmpresa',
        'DesccCosto',
        'Estado',
        'Porcentaje'
    ];
}
