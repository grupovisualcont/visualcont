<?php

namespace App\Models;

use CodeIgniter\Model;

class Empresa extends Model
{
    protected $table            = 'empresas';
    protected $primaryKey       = 'CodEmpresa';
    protected $allowedFields    = [];

    public function getEmpresa(string $CodEmpresa, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            if (!empty($CodEmpresa)) $result = $result->where('CodEmpresa', $CodEmpresa);

            if (!empty($where)) $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->asArray()->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function login($usuario, $password)
    {
        try {
            $result = $this->where('CodEmpresa', $usuario)->where('Contraseña', $password)->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
