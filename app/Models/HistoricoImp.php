<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoricoImp extends Model
{
    protected $table = 'historicoimp';

    protected $primaryKey = 'idHistImp';

    protected $allowedFields = [
        'idHistImp',
        'CodEmpresa',
        'Periodo',
        'Mes',
        'Tipo',
        'Fecha',
        'Descripcion'
    ];
}
