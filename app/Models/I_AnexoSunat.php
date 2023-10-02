<?php

namespace App\Models;

use CodeIgniter\Model;

class I_AnexoSunat extends Model
{
    protected $table = 'i_anexossunat';

    protected $primaryKey = 'IdAnexoS';

    protected $allowedFields = [];

    public function getI_AnexoSunatByTipoAnexoS($TipoAnexoS)
    {
        try {
            $result = $this->where('TipoAnexoS', $TipoAnexoS)->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
