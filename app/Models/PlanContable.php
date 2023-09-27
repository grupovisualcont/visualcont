<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanContable extends Model
{
    protected $table = 'plan_contable';

    protected $primaryKey = 'CodCuenta';

    protected $allowedFields = [
        'CodEmpresa',
        'Periodo',
        'CodCuenta',
        'CuentaPadre',
        'CuentaAjuste',
        'DescCuenta',
        'RelacionCuenta',
        'TipoResultado',
        'TipoCuenta',
        'AjusteDC',
        'Tcambio_CV',
        'TipoDebeHaber',
        'Child'
    ];
}
