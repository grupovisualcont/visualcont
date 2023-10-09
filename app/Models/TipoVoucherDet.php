<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoVoucherDet extends Model
{
    protected $table = 'tipovoucherdet';

    protected $primaryKey = 'CodTV';

    protected $allowedFields = [
        'CodTV',
        'NumItem',
        'CodEmpresa',
        'Periodo',
        'CodCuenta',
        'Debe_Haber',
        'Parametro',
        'MontoD',
        'CodMoneda',
        'CodCcosto',
        'IdActivo',
        'IdSocioN'
    ];

    public function getTipoVoucherDet(string $CodEmpresa, string $Periodo, string $CodCuenta, string $CodTV, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('tipovoucherdet.CodEmpresa', $CodEmpresa);

            if (!empty($Periodo)) $result = $result->where('tipovoucherdet.Periodo', $Periodo);

            if (!empty($CodCuenta)) $result = $result->where('tipovoucherdet.CodCuenta', $CodCuenta);

            if (!empty($CodTV)) $result = $result->where('tipovoucherdet.CodTV', $CodTV);

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

    public function actualizar($CodEmpresa, $CodTV, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($CodTV, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $CodTV, $Periodo)
    {
        try {
            $result = $this->where('CodEmpresa', $CodEmpresa);

            if (!empty($Periodo)) $result = $result->where('Periodo', $Periodo);

            $result->delete($CodTV);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
