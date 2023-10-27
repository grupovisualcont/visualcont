<?php

namespace App\Models\Web;

use CodeIgniter\Model;
use \Throwable;

class AnexoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'anexo';
    protected $primaryKey       = 'CodAnexo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'CodEmpresa',
        'DescAnexo',
        'TipoAnexo',
        'CodInterno',
        'Estado',
        'OtroDato',
        'IdTipOpeDetra'
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Se obtiene los parametros condicionales para socio de negocio
     * @return array
     */
    public function getCondiciones(): array
    {
        return $this->getAnexo(2, null);
    }

    /**
     * Devuelve los estados 
     * @return array
     */
    public function getEstados(): array
    {
        return $this->getAnexo(1, null);
    }

    /**
     * Se obtiene los anexos dependientes del tipo y filtrado para ciertas empresas
     */
    public function getAnexo(int|string $tipo, $codEmpresa = null): array
    {
        try {
            $this->where('TipoAnexo', $tipo);
            if (!empty($codEmpresa)) {
                $this->where('CodEmpresa', $codEmpresa);
            }
            $this->orderBy('CodInterno', 'ASC');
            return $this->get()->getResult();
        } catch (Throwable $ex) {
            return [];
        }
    }

}
