<?php

namespace App\Models;

use CodeIgniter\Model;

class SaldoDet extends Model
{
    protected $table = 'saldodet';

    protected $primaryKey = 'IdCobroPago';

    protected $allowedFields = [
        'CodEmpresa',
        'IdMov',
        'IdMovDet',
        'IdMovDetRef',
        'Periodo',
        'Mes',
        'TotalDetSol',
        'TotalDetDol',
        'Importado',
        'CodDocRef',
        'SerieRef',
        'NumeroRef',
        'FechaRef',
        'FlagInterno'
    ];

    public function getSaldoDet(string $CodEmpresa, int $IdCobroPago, int $IdMov, int $IdMovDet, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('saldodet.CodEmpresa', $CodEmpresa);

            if (!empty($IdCobroPago)) $result = $result->where('saldodet.IdCobroPago', $IdCobroPago);

            if (!empty($IdMov)) $result = $result->where('saldodet.IdMov', $IdMov);

            if (!empty($IdMovDet)) $result = $result->where('saldodet.IdMovDet', $IdMovDet);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->asArray()->findAll();

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

    public function actualizar($CodEmpresa, $IdCobroPago, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($IdCobroPago, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $Importado, $IdMov, $IdMovDet, $IdCobroPago)
    {
        try {
            $result = $this->where('saldodet.CodEmpresa', $CodEmpresa);

            if (!empty($IdMov)) $result = $result->where('saldodet.IdMov', $IdMov);

            if (!empty($IdMovDet)) $result = $result->where('saldodet.IdMovDet', $IdMovDet);

            if (!empty($Importado)) {
                $result = $result->where('saldodet.Importado', $Importado)->delete();
            } else {
                $result = $result->delete($IdCobroPago);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
