<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoSocioNegocio extends Model
{
    protected $table = 'tiposocionegocio';
    protected $primaryKey = 'CodTipoSN';
    protected $returnType = 'object';
    protected $allowedFields = [];

    public function getTipoSocioNegocio(string $CodTipoSN, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            if (!empty($CodTipoSN)) $result = $result->where('CodTipoSN', $CodTipoSN);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->asArray()->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
