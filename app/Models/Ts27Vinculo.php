<?php

namespace App\Models;

use CodeIgniter\Model;

class Ts27Vinculo extends Model
{
    protected $table = 'ts27_vinculo';

    protected $primaryKey = 'CodVinculo';

    protected $allowedFields = [];

    public function getTs27Vinculo(string $CodVinculo, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            if (!empty($CodVinculo)) $result = $result->where('CodVinculo', $CodVinculo);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
