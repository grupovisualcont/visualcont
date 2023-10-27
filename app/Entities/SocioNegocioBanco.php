<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SocioNegocioBanco extends Entity
{
    protected $attributes = [
        'CodSocioN',
        'CodBanco',
        'idTipoCuenta',
        'NroCuenta',
        'NroCuentaCCI',
        'Predeterminado',
        'Detraccion',
    ];
    protected $datamap = [];
    protected $dates   = [];
    protected $casts   = [];
}
