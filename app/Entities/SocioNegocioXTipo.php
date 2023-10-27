<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Models\Web\TipoSocioNegocioModel;
use Exception;

class SocioNegocioXTipo extends Entity
{
    protected $attributes = [
        'CodTipoSN',
        'CodSocioN',
    ];

    protected $datamap = [];
    protected $dates   = [];
    protected $casts   = [
        'CodTipoSN' => 'integer',
        'CodSocioN' => 'integer',
    ];

}
