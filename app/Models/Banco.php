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

    public function getBanco($CodEmpresa, $columnas)
    {
        try {
            $result = $this->select($columnas)->where('CodEmpresa', $CodEmpresa)->orderBy('Codbanco', 'ASC')->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
