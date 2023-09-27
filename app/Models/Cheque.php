<?php

namespace App\Models;

use CodeIgniter\Model;

class Cheque extends Model
{
    protected $table = 'cheque';

    protected $primaryKey = 'idCheque';

    protected $allowedFields = [
        'CodBanco',
        'CodEmpresa',
        'CodCheque',
        'DescCheque',
        'nroinicial',
        'nrOfinal',
        'numerador',
        'Estado'
    ];
}
