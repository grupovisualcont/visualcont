<?php

namespace App\Models\Web;

use CodeIgniter\Model;

class TipoSocioNegocioModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tiposocionegocio';
    protected $primaryKey       = 'CodTipoSN';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'DescTipoSN'
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
