<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoPago extends Model
{
    protected $table = 'tipopago';

    protected $primaryKey = 'CodTipoPago';

    protected $allowedFields = [];

    public function getTipoPago(string $CodTipoPago, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas, FALSE);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            if (!empty($CodTipoPago)) $result = $result->where('CodTipoPago', $CodTipoPago);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
