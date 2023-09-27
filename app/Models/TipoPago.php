<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoPago extends Model
{
    protected $table = 'tipopago';

    protected $primaryKey = 'CodTipoPago';

    protected $allowedFields = [];
}
