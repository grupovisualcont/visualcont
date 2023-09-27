<?php

namespace App\Models;

use CodeIgniter\Model;

class Documento extends Model
{
    protected $table = 'documento';

    protected $primaryKey = 'CodDocumento';

    protected $allowedFields = [
        'CodDocumento',
        'CodClaseDoc',
        'CodEmpresa',
        'DescDocumento',
        'Serie',
        'Numero',
        'CodSunat',
        'origen',
        'vanalRegistrode',
        'Estado'
    ];
}
