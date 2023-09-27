<?php

namespace App\Models;

use CodeIgniter\Model;

class EntidadFinanciera extends Model
{
    protected $table = 'entidadfinanciera';

    protected $primaryKey = 'CodEntidad';

    protected $allowedFields = [];
}
