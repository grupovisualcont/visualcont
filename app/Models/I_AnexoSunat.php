<?php

namespace App\Models;

use CodeIgniter\Model;

class I_AnexoSunat extends Model
{
    protected $table = 'i_anexossunat';

    protected $primaryKey = 'IdAnexoS';

    protected $allowedFields = [];

    public function getI_AnexoSunat(int $IdAnexoS, string $TipoAnexoS, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }
            
            if (!empty($IdAnexoS)) $result = $result->where('IdAnexoS', $IdAnexoS);

            if (!empty($TipoAnexoS)) $result = $result->where('TipoAnexoS', $TipoAnexoS);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
