<?php

namespace App\Models;

use CodeIgniter\Model;

class Moneda extends Model
{
    protected $table            = 'moneda';
    protected $primaryKey       = 'CodMoneda';
    protected $returnType       = 'object';
    protected $allowedFields    = [];

    public function getMoneda(string $CodMoneda, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            if (!empty($CodMoneda)) $result = $result->where('CodMoneda', $CodMoneda);

            if (!empty($where)) $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->asArray()->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autoCompletado($busqueda)
    {
        $this->select('
            CodMoneda as id,
            DescMoneda as text
        ');
        if (!empty($busqueda)) {
            $this->like('DescMoneda', $busqueda);
        }
        return $this->get()->getResult();
    }
}
