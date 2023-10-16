<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientoObs extends Model
{
    protected $table = 'movimientoobs';

    protected $primaryKey = 'IdMovObservacion';

    protected $allowedFields = [
        'CodEmpresa',
        'Origen',
        'Tipo',
        'CodDocumento',
        'Afecto',
        'Inafecto',
        'Exonerado',
        'Igv',
        'Icbp',
        'Descuento',
        'Otro_Tributo',
        'TotalS',
        'TotalD',
        'Caja'
    ];

    public function getMovimientoObs(string $CodEmpresa, int $IdMovObservacion, string $Origen, string $Tipo, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('movimientoobs.CodEmpresa', $CodEmpresa);

            if (!empty($IdMovObservacion)) $result = $result->where('movimientoobs.IdMovObservacion', $IdMovObservacion);

            if (!empty($Origen)) $result = $result->where('movimientoobs.Origen', $Origen);

            if (!empty($Tipo)) $result = $result->where('movimientoobs.Tipo', $Tipo);

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function actualizar($CodEmpresa, $Origen, $Tipo, $IdMovObservacion, $data)
    {
        try {
            $result = $this->where('movimientoobs.CodEmpresa', $CodEmpresa);

            if (!empty($Origen)) $result = $result->where('movimientoobs.Origen', $Origen);

            if (!empty($Tipo)) $result = $result->where('movimientoobs.Tipo', $Tipo);

            $result = $result->update($IdMovObservacion, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
