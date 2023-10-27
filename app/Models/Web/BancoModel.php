<?php

namespace App\Models\Web;

use CodeIgniter\Model;
use App\Entities\Banco;

class BancoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'banco';
    protected $primaryKey       = 'Codbanco';
    protected $useAutoIncrement = false;
    protected $returnType       = Banco::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'CodEmpresa',
        'CodEntidad',
        'Abreviatura',
        'CodMoneda',
        'CtaCte',
        'SaldoCtaCte',
        'CodCuenta',
        'Periodo',
        'Propio',
        'PagoDetraccion',
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
