<?php

namespace App\Models;

use CodeIgniter\Model;

class CondicionPago extends Model
{
    protected $table = 'condicionpago';

    protected $primaryKey = 'codcondpago';

    protected $allowedFields = [
        'codcondpago',
        'CodEmpresa',
        'desccondpago',
        'comentario',
        'con_cre',
        'Ndias',
        'carga_inicial',
        'Tipo',
        'Estado'
    ];

    public function getCondicionPago(string $CodEmpresa, string $codcondpago, string $columnas, array $join, string $where, string $orderBy)
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

            if (!empty($codcondpago)) $result = $result->where('codcondpago', $codcondpago);

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

    public function actualizar($CodEmpresa, $codcondpago, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($codcondpago, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $codcondpago)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($codcondpago);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autoCompletado($busqueda, $codEmpresa)
    {
        $this->select('
            codcondpago as id,
            desccondpago as text
        ');
        if (!empty($busqueda)) {
            $this->like('desccondpago', $busqueda);
        }
        $this->where('codEmpresa', $codEmpresa);
        $this->limit(LIMITE_AUTOCOMPLETADO);
        return $this->get()->getResult();
    }
}
