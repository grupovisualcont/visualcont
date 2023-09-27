<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoDocumentoIdentidad extends Model
{
    protected $table = 'tipodocidentidad';

    protected $primaryKey = 'CodTipoDoc';

    protected $allowedFields = [];
}
