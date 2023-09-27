<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoComprobante extends Model
{
    protected $table = 'tipocomprobante';

    protected $primaryKey = 'CodComprobante';

    protected $allowedFields = [];
}
