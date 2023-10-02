<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanContable extends Model
{
    protected $table = 'plan_contable';

    protected $primaryKey = 'CodCuenta';

    protected $allowedFields = [
        'CodEmpresa',
        'Periodo',
        'CodCuenta',
        'CuentaPadre',
        'CuentaAjuste',
        'DescCuenta',
        'RelacionCuenta',
        'TipoResultado',
        'TipoCuenta',
        'AjusteDC',
        'Tcambio_CV',
        'TipoDebeHaber',
        'Child'
    ];

    public function getPlanContable(string $CodEmpresa, string $Periodo, string $CodCuenta, string $columnas, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

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

    public function getCuentasGastos($CodEmpresa)
    {
        try {
            $result = $this->select('CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled')
                ->where('CodEmpresa', $CodEmpresa)
                ->where('Periodo', date('Y'))
                ->where('CodCuenta LIKE "6%"')
                ->orderBy('CodCuenta', 'ASC')
                ->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getCuentasDepreciacion($CodEmpresa)
    {
        try {
            $result = $this->select('CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled')
                ->where('CodEmpresa', $CodEmpresa)
                ->where('Periodo', date('Y'))
                ->where('CodCuenta LIKE "3%"')
                ->orderBy('CodCuenta', 'ASC')
                ->findAll();

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

    public function actualizar($CodEmpresa, $Periodo, $CodCuenta, $where, $data)
    {
        try {
            $result = $this->where('CodEmpresa', $CodEmpresa)->where('Periodo', $Periodo);

            if (!empty($where)) $result->where($where);

            $result->update($CodCuenta, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $Periodo, $CodCuenta, $where)
    {
        try {
            $result = $this->where('CodEmpresa', $CodEmpresa)->where('Periodo', $Periodo);

            if (!empty($where)) $result->where($where);

            $result->delete($CodCuenta);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel($CodEmpresa)
    {
        try {
            $result = $this->select('Periodo, CodCuenta, DescCuenta, CuentaPadre, CuentaAjuste,
                        CASE RelacionCuenta WHEN 0 THEN "Ninguno" WHEN 1 THEN "Cuenta Corriente" WHEN 2 THEN "Centro de Costo" WHEN 3 THEN "Ambos" WHEN 4 THEN "Activo Fijo" END AS RelacionCuenta,
                        CASE TipoResultado WHEN 0 THEN "Ninguno" WHEN 1 THEN "Inventario" WHEN 2 THEN "Resultado x Naturaleza" WHEN 3 THEN "Resultado x Funcion" WHEN 4 THEN "Resultado x Naturaleza y Funcion" END AS TipoResultado', FALSE)
                ->where('CodEmpresa', $CodEmpresa)
                ->orderBy('CodCuenta', 'ASC')
                ->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf($CodEmpresa)
    {
        try {
            $result = $this->select('Periodo, CodCuenta, DescCuenta, CuentaPadre, CuentaAjuste,
                        CASE RelacionCuenta WHEN 0 THEN "Ninguno" WHEN 1 THEN "Cuenta Corriente" WHEN 2 THEN "Centro de Costo" WHEN 3 THEN "Ambos" WHEN 4 THEN "Activo Fijo" END AS RelacionCuenta,
                        CASE TipoResultado WHEN 0 THEN "Ninguno" WHEN 1 THEN "Inventario" WHEN 2 THEN "Resultado x Naturaleza" WHEN 3 THEN "Resultado x Funcion" WHEN 4 THEN "Resultado x Naturaleza y Funcion" END AS TipoResultado', FALSE)
                ->where('CodEmpresa', $CodEmpresa)
                ->orderBy('CodCuenta', 'ASC')
                ->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
