<?php

namespace App\Models;

use CodeIgniter\Model;

class Moneda extends Model
{
    protected $table            = 'moneda';
    protected $primaryKey       = 'CodMoneda';
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
}
