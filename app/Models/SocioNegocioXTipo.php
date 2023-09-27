<?php

namespace App\Models;

use CodeIgniter\Model;

class SocioNegocioXTipo extends Model
{
    protected $table = 'socionegocioxtipo';

    protected $primaryKey = 'CodTipoSN, IdSocioN';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'CodTipoSN',
        'IdSocioN'
    ];
}
