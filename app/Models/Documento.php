<?php

namespace App\Models;

use CodeIgniter\Model;

class Documento extends Model
{
    protected $table = 'documento';

    protected $primaryKey = 'CodDocumento';

    protected $allowedFields = [
        'CodDocumento',
        'CodClaseDoc',
        'CodEmpresa',
        'DescDocumento',
        'Serie',
        'Numero',
        'CodSunat',
        'origen',
        'vanalRegistrode',
        'Estado'
    ];

    public function getDocumento($CodEmpresa, $CodDocumento, $Origen, $join, $columnas, $where, $orderBy)
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

            if (!empty($CodDocumento)) $result = $result->where('UPPER(CodDocumento)', $CodDocumento);

            if (!empty($Origen)) $result = $result->where($Origen);

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

    public function actualizar($CodEmpresa, $CodDocumento, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($CodDocumento, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $CodDocumento)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($CodDocumento);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
