<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class TipoDocumento
 */
class TipoDocumento extends Model
{
    
    public function __construct()
    {
        parent::__construct();
    }

    protected $table            = 'tipodocumentos';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        ''
    ];

}