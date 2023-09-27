<?php

namespace App\Models;

use CodeIgniter\Model;

class Banco extends Model
{
    protected $table = 'banco';

    protected $primaryKey = 'Codbanco';

    protected $allowedFields = [
        'Codbanco',
        'CodEmpresa',
        'CodEntidad',
        'abreviatura',
        'CodMoneda',
        'ctacte',
        'salDoctacte',
        'codcuenta',
        'Periodo',
        'Propio',
        'PagoDetraccion'
    ];
}
