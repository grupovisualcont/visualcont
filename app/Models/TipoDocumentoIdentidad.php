<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoDocumentoIdentidad extends Model
{
    protected $table = 'tipodocidentidad';

    protected $primaryKey = 'CodTipoDoc';

    protected $allowedFields = [];

    public function getTipoDocumentoIdentidad(string $CodTipoDoc, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            if (!empty($CodTipoDoc)) $result = $result->where('CodTipoDoc', $CodTipoDoc);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
