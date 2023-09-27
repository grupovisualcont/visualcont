<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoVoucherDet extends Model
{
    protected $table = 'tipovoucherdet';

    protected $primaryKey = 'CodTV';

    protected $allowedFields = [
        'CodTV',
        'NumItem',
        'CodEmpresa',
        'Periodo',
        'CodCuenta',
        'Debe_Haber',
        'Parametro',
        'MontoD',
        'CodMoneda',
        'CodCcosto',
        'IdActivo',
        'IdSocioN'
    ];
}
