<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoricoImp extends Model
{
    protected $table = 'historicoimp';

    protected $primaryKey = 'idHistImp';

    protected $allowedFields = [
        'idHistImp',
        'CodEmpresa',
        'Periodo',
        'Mes',
        'Tipo',
        'Fecha',
        'Descripcion'
    ];

    public function getHistoricoImp(string $CodEmpresa, int $idHistImp, string $columnas, array $join, string $where, string $orderBy)
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

            if (!empty($idHistImp)) $result = $result->where('idHistImp', $idHistImp);

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

    public function eliminar($CodEmpresa, $idHistImp)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($idHistImp);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
