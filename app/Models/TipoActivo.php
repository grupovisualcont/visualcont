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

    public function getTipoActivo($CodEmpresa, $columnas, $where)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            $result = $result->where('CodEmpresa', $CodEmpresa);

            if (!empty($where)) $result = $result->where($where);

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
