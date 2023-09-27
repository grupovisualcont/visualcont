<?php

namespace App\Models;

use CodeIgniter\Model;

class ConceptoPres extends Model
{
    protected $table = 'conceptopres';

    protected $primaryKey = 'CodConceptoPres';

    protected $allowedFields = [
        'CodConceptoPres',
        'CodEmpresa',
        'descConceptoPres',
        'CodCuenta'
    ];
}
