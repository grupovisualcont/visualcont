<?php

namespace App\Models\Web;

use CodeIgniter\Model;

class TipoDocumentoIdentidadModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tipodocidentidad';
    protected $primaryKey       = 'CodTipoDoc';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'Tipo',
        'DesDocumento',
        'TipoDato',
        'N_tip',
        'N_lon',
        'Bcp',
        'Bbva',
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
