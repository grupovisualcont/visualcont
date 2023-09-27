<?php

namespace App\Models;

use CodeIgniter\Model;

class Anexo extends Model
{
    protected $table = 'anexos';

    protected $primaryKey = 'IdAnexo';

    protected $allowedFields = [
        'IdAnexo',
        'CodEmpresa',
        'DescAnexo',
        'TipoAnexo',
        'CodInterno',
        'Estado',
        'OtroDato',
        'IdTipOpeDetra'
    ];
}
