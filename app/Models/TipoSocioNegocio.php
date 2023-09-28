<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoSocioNegocio extends Model
{
    protected $table = 'tiposocionegocio';
    protected $primaryKey = 'CodTipoSN';
    protected $returnType = 'object';
    protected $allowedFields = [];
}
