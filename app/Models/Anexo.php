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

    public function autoCompletado($busqueda, $codEmpresa, $tipo)
    {
        $this->select('
            IdAnexo as id,
            DescAnexo as text
        ');
        $this->where('TipoAnexo', $tipo);
        $this->where('codEmpresa', $codEmpresa);
        if (!empty($busqueda)) {
            $this->like('DescAnexo', $busqueda);
        }
        $this->orderBy('CodInterno', 'ASC');
        return $this->get()->getResult();
    }
}
