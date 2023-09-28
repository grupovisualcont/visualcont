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
