<?php

namespace App\Models;

use CodeIgniter\Model;

class Detraccion extends Model
{
    protected $table = 'detraccion';

    protected $primaryKey = 'IdDetraccion';

    protected $allowedFields = [];

    public function getDetraccion(string $CodEmpresa, int $IdDetraccion, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('CodEmpresa', $CodEmpresa);

            if (!empty($IdDetraccion)) $result = $result->where('IdDetraccion', $IdDetraccion);

            if (!empty($where)) $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->asArray()->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
