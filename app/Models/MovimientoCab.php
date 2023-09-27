<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientoCab extends Model
{
    protected $table = 'movimientocab';

    protected $primaryKey = 'IdMov';

    protected $allowedFields = [
        'IdMov',
        'CodEmpresa',
        'Periodo',
        'Mes',
        'Codmov',
        'CodTV',
        'IdMovRef',
        'IdMovAplica',
        'FecContable',
        'TotalSol',
        'TotalDol',
        'Origen',
        'Glosa',
        'Estado',
        'Importado',
        'codOtroSis',
        'ValorTC',
        'Detraccion',
        'FlagInterno'
    ];
}
