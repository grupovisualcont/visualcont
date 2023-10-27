<?php

namespace App\Models\Web;

use CodeIgniter\Model;
use Throwable;

class SocioNegocioBancoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'socionegociobanco';
    protected $primaryKey       = 'IdCtaCte';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'CodSocioN',
        'CodBanco',
        'idTipoCuenta',
        'NroCuenta',
        'NroCuentaCCI',
        'Predeterminado',
        'Detraccion',
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getDetalle($CodSocioN): array
    {
        try {
            return $this->select("*, CONCAT(banco.CodBanco, ' - ', banco.Abreviatura) as Banco")
                ->join('banco', 'socionegociobanco.CodBanco = banco.CodBanco', 'inner')
                ->where('CodSocioN', $CodSocioN)
                ->get()
                ->getResult();
        } catch (Throwable $ex) {
            return [];
        }
    }

}
