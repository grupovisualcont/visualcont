<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoVoucherCab extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tipovouchercab';
    protected $primaryKey       = 'CodTv';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'CodTV',
        'CodEmpresa',
        'DescVoucher',
        'GlosaVoucher',
        'CargaInicial',
        'Norden',
        'Tipo',
        'CodLibro',
        'CodEFE',
        'CodTVcaja',
        'FlagInterno'
    ];

    public function getTipoVoucherCab(string $CodEmpresa, string $CodTV, array|int $Tipo, string $columnas, array $join, string $where, string $orderBy)
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

            if (!empty($CodTV)) $result = $result->where('CodTV', $CodTV);

            if (!is_array($Tipo) && !empty($Tipo)) $result = $result->where('Tipo', $Tipo);

            if (is_array($Tipo) && count($Tipo) > 0) $result = $result->where('Tipo IN (' . implode(', ', $Tipo) . ')');

            if (!empty($where)) $result->where($where);

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
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function actualizar($CodEmpresa, $CodTV, $data)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->update($CodTV, $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function eliminar($CodEmpresa, $CodTV)
    {
        try {
            $this->where('CodEmpresa', $CodEmpresa)->delete($CodTV);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autoCompletado($CodTV = '', $DescVoucher = '')
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('TIPOVOUCHERCAB');
        $builder->select('CodTV as id, DescVoucher as text');
        // $builder->table('TIPOVOUCHERCAB');
        $builder->like('CodTV', $CodTV);
        $builder->like('DescVoucher', $DescVoucher);
        $builder->whereIn('Tipo', ['3', '4']);
        $builder->orderBy('DescVoucher');
        return $builder->get()->getResult();
    }
}
