<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoCambio extends Model
{
    protected $table            = 'tipocambio';
    protected $primaryKey       = 'FechaTipoCambio';
    protected $allowedFields = [
        'FechaTipoCambio',
        'CodEmpresa',
        'CodMoneda',
        'ValorCompra',
        'ValorVenta',
        'Estado'
    ];

    public function getTipoCambio(string $CodEmpresa, string $FechaTipoCambio, string $columnas, array $join, string $where, string $orderBy)
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

            if (!empty($FechaTipoCambio)) $result = $result->where('FechaTipoCambio', strpos($FechaTipoCambio, ' 00:00:00') !== FALSE ? $FechaTipoCambio : $FechaTipoCambio . ' 00:00:00');

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

    public function actualizar($CodEmpresa, $FechaTipoCambio, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($FechaTipoCambio, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
