<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoActivo extends Model
{
    protected $table = 'tipoactivo';

    protected $primaryKey = 'codTipoActivo';

    protected $allowedFields = [
        'codTipoActivo',
        'CodEmpresa',
        'descTipoActivo'
    ];

    public function getTipoActivo(string $CodEmpresa, string $codTipoActivo, string $columnas, array $join, string $where, string $orderBy)
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

            if (!empty($codTipoActivo)) $result = $result->where('codTipoActivo', $codTipoActivo);

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

    public function actualizar($CodEmpresa, $codTipoActivo, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($codTipoActivo, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $codTipoActivo)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($codTipoActivo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
