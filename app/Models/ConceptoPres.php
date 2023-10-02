<?php

namespace App\Models;

use CodeIgniter\Model;

class ConceptoPres extends Model
{
    protected $table = 'conceptopres';

    protected $primaryKey = 'CodConceptoPres';

    protected $allowedFields = [
        'CodConceptoPres',
        'CodEmpresa',
        'descConceptoPres',
        'CodCuenta'
    ];

    public function getConceptoPres(String $CodEmpresa, String $CodConceptoPres, String $columnas, Array $join, String $where, String $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas, FALSE);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('CodEmpresa', $CodEmpresa);

            if (!empty($CodConceptoPres)) $result = $result->where('CodConceptoPres', $CodConceptoPres);

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

    public function actualizar($CodEmpresa, $CodConceptoPres, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($CodConceptoPres, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $CodConceptoPres)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($CodConceptoPres);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
