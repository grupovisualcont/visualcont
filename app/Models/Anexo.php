<?php

namespace App\Models;

use CodeIgniter\Model;

class Anexo extends Model
{
    protected $table = 'anexos';

    protected $primaryKey = 'IdAnexo';

    protected $allowedFields = [
        'IdAnexo',
        'CodEmpresa',
        'DescAnexo',
        'TipoAnexo',
        'CodInterno',
        'Estado',
        'OtroDato',
        'IdTipOpeDetra'
    ];

    public function getAnexoByTipoAnexo($CodEmpresa, $TipoAnexo, $OtroDato, $orderBy)
    {
        try {
            $result = $this->where('CodEmpresa', $CodEmpresa)->where('TipoAnexo', $TipoAnexo);

            if (!empty($OtroDato)) $result = $result->where('OtroDato', $OtroDato);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
