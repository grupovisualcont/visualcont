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
}
