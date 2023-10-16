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

    public function getAmarre(string $CodEmpresa, string $Periodo, string $CodCuenta, string $columnas, array $join, string $where, string $orderBy)
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

            if (!empty($Periodo)) $result = $result->where('Periodo', $Periodo);

            if (!empty($CodCuenta)) $result = $result->where('CodCuenta', $CodCuenta);

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

            $result = $this->insertID();

            return $result;
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
