<?php

namespace App\Models;

use CodeIgniter\Model;

class VoucherPres extends Model
{
    protected $table = 'voucherpres';

    protected $primaryKey = 'CodVoucherPre';

    protected $allowedFields = [
        'CodVoucherPre',
        'CodEmpresa',
        'DescVoucherPre'
    ];

    public function getVoucherPres(String $CodEmpresa, String $CodVoucherPre, String $columnas, Array $join, String $where, String $orderBy)
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

            if (!empty($CodVoucherPre)) $result = $result->where('CodVoucherPre', $CodVoucherPre);

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

    public function actualizar($CodEmpresa, $CodVoucherPre, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($CodVoucherPre, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $CodVoucherPre)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($CodVoucherPre);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
