<?php

namespace App\Models;

use CodeIgniter\Model;

class Amarre extends Model
{
    protected $table = 'amarres';

    protected $primaryKey = 'CodCuenta';

    protected $allowedFields = [
        'CodCuenta',
        'Periodo',
        'CodEmpresa',
        'CuentaDebe',
        'CuentaHaber',
        'Porcentaje'
    ];

    public function getAmarre($CodEmpresa, $Periodo, $CodCuenta, $columnas, $where)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            $result = $result->where('CodEmpresa', $CodEmpresa)->where('Periodo', $Periodo)->where('CodCuenta', $CodCuenta);

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

    public function eliminar($CodEmpresa, $Periodo, $CodCuenta)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->where('Periodo', $Periodo)->where('CodCuenta', $CodCuenta)->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
