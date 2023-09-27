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

    public function insertar($post)
    {
        try {
            $this->insert($post);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getTipoCambioByFecha($CodEmpresa, $fecha)
    {
        try {
            $result = $this->where('CodEmpresa', $CodEmpresa)->where('FechaTipoCambio', $fecha . ' 00:00:00')->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
