<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoSocioNegocio extends Model
{
    protected $table = 'tiposocionegocio';

    protected $primaryKey = 'CodTipoSN';

    protected $allowedFields = [];

    public function getTipoSocioNegocio()
    {
        try {
            $result = $this->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
