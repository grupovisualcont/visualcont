<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanContableEquiv extends Model
{
    protected $table = 'plan_contable_equiv';

    protected $primaryKey = 'CodCuenta';

    protected $allowedFields = [
        'Periodo',
        'CodCuenta',
        'CodEmpresa',
        'CodCuentaEquiv',
        'DescCuenta'
    ];
}
