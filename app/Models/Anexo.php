<?php

namespace App\Models;

use CodeIgniter\Model;

class Anexo extends Model
{
    protected $table = 'anexos';

    protected $primaryKey = 'IdAnexo';

    protected $allowedFields = [
        'IdAnexo',
        'CodEmpresa',
        'DescAnexo',
        'TipoAnexo',
        'CodInterno',
        'Estado',
        'OtroDato',
        'IdTipOpeDetra'
    ];

    public function getAnexo(string $CodEmpresa, int $IdAnexo, array|int $TipoAnexo, string $OtroDato, string $columnas, array $join, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas, FALSE);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $this->where('CodEmpresa', $CodEmpresa);

            if (!empty($IdAnexo)) $result = $result->where('IdAnexo', $IdAnexo);

            if (!is_array($TipoAnexo) && !empty($TipoAnexo)) $result = $result->where('TipoAnexo', $TipoAnexo);

            if (is_array($TipoAnexo) && count($TipoAnexo) > 0) $result = $result->where('TipoAnexo IN (' . implode(', ', $TipoAnexo) . ')');

            if (!empty($OtroDato)) $result = $result->where('OtroDato', $OtroDato);

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

    public function actualizar($CodEmpresa, $IdAnexo, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($IdAnexo, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $IdAnexo)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($IdAnexo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autoCompletado($busqueda, $codEmpresa, $tipo)
    {
        $this->select('
            IdAnexo as id,
            DescAnexo as text
        ');
        $this->where('TipoAnexo', $tipo);
        $this->where('codEmpresa', $codEmpresa);
        if (!empty($busqueda)) {
            $this->like('DescAnexo', $busqueda);
        }
        $this->orderBy('CodInterno', 'ASC');
        return $this->get()->getResult();
    }
}
