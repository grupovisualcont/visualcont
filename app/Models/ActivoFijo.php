<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivoFijo extends Model
{
    protected $table = 'activosfijos';

    protected $primaryKey = 'IdActivo';

    protected $allowedFields = [
        'codActivo',
        'CodEmpresa',
        'codTipoActivo',
        'descripcion',
        'marca',
        'modelo',
        'serie',
        'fechaAdqui',
        'fechaInicio',
        'fechaRetiro',
        'depresiacion',
        'estado',
        'AnioEstimado',
        'CtaCtableGasto',
        'CtaCtableDepreciacion',
        'CodCcosto',
        'codubigeo',
        'Localidad',
        'Direccion',
        'Responsable',
        'idSituacion',
        'IdCatalogo',
        'IdTipoActivo',
        'IdEstadoActivo',
        'IdMetodo',
        'CodSunatExi',
        'ArrNumero',
        'ArrFecha',
        'ArrCuota',
        'ArrMonto'
    ];

    public function getActivoFijo(string $CodEmpresa, int $IdActivo, string $codActivo, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('activosfijos.CodEmpresa', $CodEmpresa);
            
            if (!empty($IdActivo)) $result = $result->where('activosfijos.IdActivo', $IdActivo);

            if (!empty($codActivo)) $result = $result->where('activosfijos.codActivo', $codActivo);

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

    public function actualizar($CodEmpresa, $IdActivo, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($IdActivo, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $IdActivo)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($IdActivo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getActivoFijoPDF($CodEmpresa)
    {
        try {
            $result = $this->select()
                ->join('', '')
                ->where('CodEmpresa', $CodEmpresa)
                ->orderBy('activosfijos.IdActivo', 'ASC')
                ->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getActivoFijoExcel($CodEmpresa)
    {
        try {
            $result = $this->select('activosfijos.codActivo, activosfijos.descripcion, t.descTipoActivo, activosfijos.marca, activosfijos.modelo, activosfijos.serie')
                ->join('tipoactivo t', 'activosfijos.codTipoActivo = t.codTipoActivo AND activosfijos.CodEmpresa = t.CodEmpresa')
                ->where('activosfijos.CodEmpresa', $CodEmpresa)
                ->orderBy('', 'ASC')
                ->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
