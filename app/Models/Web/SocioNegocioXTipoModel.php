<?php

namespace App\Models\Web;

use CodeIgniter\Model;
use App\Entities\SocioNegocioXTipo;

class SocioNegocioXTipoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'socionegocioxtipo';
    protected $primaryKey       = 'CodTipoSN, CodSocioN';
    protected $useAutoIncrement = true;
    protected $returnType       = SocioNegocioXTipo::class;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'CodTipoSN',
        'CodSocioN'
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
