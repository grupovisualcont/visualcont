<?php

namespace App\Models;

use CodeIgniter\Model;

class SaldoDet extends Model
{
    protected $table = 'saldodet';

    protected $primaryKey = 'IdCobroPago';

    protected $allowedFields = [
        'CodEmpresa',
        'IdMov',
        'IdMovDet',
        'IdMovDetRef',
        'Periodo',
        'Mes',
        'TotalDetSol',
        'TotalDetDol',
        'Importado',
        'CodDocRef',
        'SerieRef',
        'NumeroRef',
        'FechaRef',
        'FlagInterno'
    ];
}
