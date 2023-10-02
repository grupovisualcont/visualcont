<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoComprobante extends Model
{
    protected $table = 'tipocomprobante';

    protected $primaryKey = 'CodComprobante';

    protected $allowedFields = [];

    public function getTipoComprobante($join, $columnas, $where, $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
