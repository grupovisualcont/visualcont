<?php

namespace App\Models\Web;

use CodeIgniter\Model;
use \Throwable;

class TipoPersonaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tipopersona';
    protected $primaryKey       = 'CodTipPer';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'DescPer'
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
