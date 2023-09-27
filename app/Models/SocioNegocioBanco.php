<?php

namespace App\Models;

use CodeIgniter\Model;

class SocioNegocioBanco extends Model
{
    protected $table = 'socionegociobancos';

    protected $primaryKey = 'IdCtaCte, IdSocion';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'IdSocion',
        'CodBanco',
        'idTipoCuenta',
        'NroCuenta',
        'NroCuentaCCI',
        'Predeterminado'
    ];
}
