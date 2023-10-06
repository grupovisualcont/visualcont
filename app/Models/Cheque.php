<?php

namespace App\Models;

use CodeIgniter\Model;

class Cheque extends Model
{
    protected $table = 'cheque';

    protected $primaryKey = 'idCheque';

    protected $allowedFields = [
        'CodBanco',
        'CodEmpresa',
        'CodCheque',
        'DescCheque',
        'nroinicial',
        'nrOfinal',
        'numerador',
        'Estado'
    ];

    public function getCheque(string $CodEmpresa, string $CodBanco, string $columnas, array $join, string $where, string $orderBy)
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

            if (!empty($CodBanco)) $result = $result->where('CodBanco', $CodBanco);

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

    public function actualizar($CodEmpresa, $idCheque, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($idCheque, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $CodBanco, $where)
    {
        try {
            $result = $this->where('CodEmpresa', $CodEmpresa)->where('CodBanco', $CodBanco);

            if (!empty($where)) $result = $result->where($where);

            $result->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
