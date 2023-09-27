<?php

namespace App\Models;

use CodeIgniter\Model;

class Amarre extends Model
{
    protected $table = 'amarres';

    protected $primaryKey = 'CodCuenta';

    protected $allowedFields = [
        'CodCuenta',
        'Periodo',
        'CodEmpresa',
        'CuentaDebe',
        'CuentaHaber',
        'Porcentaje'
    ];
}
