<?php

namespace App\Models;

use CodeIgniter\Model;

class CentroCosto extends Model
{
    protected $table = 'centrocosto';

    protected $primaryKey = 'CodcCosto';

    protected $allowedFields = [
        'CodcCosto',
        'CodEmpresa',
        'DesccCosto',
        'Estado',
        'Porcentaje'
    ];

    public function getCentroCosto($CodEmpresa, $CodcCosto, $Estado, $join, $columnas, $where, $like, $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas, FALSE);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('CodEmpresa', $CodEmpresa);

            if (!empty($CodcCosto)) $result = $result->where('CodcCosto', $CodcCosto);

            if (!empty($Estado)) $result = $result->where('Estado', $Estado);

            if (!empty($like)) $result = $result->where('CodcCosto LIKE "%' . $like . '%"');

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function agregar($data)
    {
        try {
            $this->insert($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function actualizar($CodEmpresa, $CodcCosto, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($CodcCosto, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $CodcCosto)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($CodcCosto);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
