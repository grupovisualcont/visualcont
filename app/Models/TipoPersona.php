<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoPersona extends Model
{
    protected $table = 'tipopersona';

    protected $primaryKey = 'CodTipPer';

    protected $allowedFields = [];

    public function getTipoPersona()
    {
        try {
            $result = $this->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
