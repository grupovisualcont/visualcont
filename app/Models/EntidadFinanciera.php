<?php

namespace App\Models;

use CodeIgniter\Model;

class EntidadFinanciera extends Model
{
    protected $table = 'entidadfinanciera';

    protected $primaryKey = 'CodEntidad';

    protected $allowedFields = [];

    public function getEntidadFinanciera(string $CodEntidad, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            if (!empty($CodEntidad)) $result = $result->where('CodEntidad', $CodEntidad);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
