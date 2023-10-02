<?php

namespace App\Models;

use CodeIgniter\Model;

class Moneda extends Model
{
    protected $table            = 'moneda';
    protected $primaryKey       = 'CodMoneda';
    protected $returnType       = 'object';
    protected $allowedFields    = [];

    public function getMoneda($columnas, $where)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (!empty($where)) $result->where($where);

            $result = $result->findAll();

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
