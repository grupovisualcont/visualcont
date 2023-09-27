<?php

namespace App\Models;

use CodeIgniter\Model;

class Empresa extends Model
{
    protected $table            = 'empresas';
    protected $primaryKey       = 'CodEmpresa';
    protected $allowedFields    = [];

    public function login($usuario, $password)
    {
        try {
            $result = $this->where('CodEmpresa', $usuario)->where('Contraseña', $password)->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getEmpresaByCodEmpresa($columnas, $CodEmpresa)
    {
        try {
            $result = $this->select($columnas)->where('CodEmpresa', $CodEmpresa)->findAll();

            return $result[0];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
