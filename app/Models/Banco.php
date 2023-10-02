<?php

namespace App\Models;

use CodeIgniter\Model;

class Banco extends Model
{
    protected $table = 'banco';

    protected $primaryKey = 'Codbanco';

    protected $allowedFields = [
        'Codbanco',
        'CodEmpresa',
        'CodEntidad',
        'abreviatura',
        'CodMoneda',
        'ctacte',
        'salDoctacte',
        'codcuenta',
        'Periodo',
        'Propio',
        'PagoDetraccion'
    ];

    public function getBanco(string $CodEmpresa, string $Codbanco, string $Periodo, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('banco.CodEmpresa', $CodEmpresa);

            if (!empty($Codbanco)) $result = $result->where('banco.Codbanco', $Codbanco);

            if (!empty($Periodo)) $result = $result->where('banco.Periodo', $Periodo);

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

    public function actualizar($CodEmpresa, $Codbanco, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($Codbanco, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $Codbanco, $Periodo)
    {
        try {
            $result = $this->where('CodEmpresa', $CodEmpresa);

            if (!empty($Periodo)) $result = $result->where('Periodo', $Periodo);

            $result->delete($Codbanco);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
