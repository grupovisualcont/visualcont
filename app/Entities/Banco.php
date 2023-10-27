<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Banco extends Entity
{
    protected $attributes = [
        'CodEmpresa'    => 'string',
        'CodEntidad'    => 'string',
        'Abreviatura'   => 'string',
        'CodMoneda'     => 'string',
        'CtaCte'        => 'string',
        'SaldoCtaCte'   => 'string',
        'CodCuenta'     => 'string',
        'Periodo'       => 'string',
        'Propio'        => 'string',
        'PagoDetraccion'=> 'string',
    ];
    protected $datamap = [];
    protected $dates   = [];
    protected $casts   = [];
}
