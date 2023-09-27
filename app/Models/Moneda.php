<?php

namespace App\Models;

use CodeIgniter\Model;

class Moneda extends Model
{
    protected $table            = 'moneda';
    protected $primaryKey       = 'CodMoneda';
    protected $allowedFields    = [];
}
