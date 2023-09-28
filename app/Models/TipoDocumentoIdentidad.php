<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoDocumentoIdentidad extends Model
{
    protected $table = 'tipodocidentidad';

    protected $primaryKey = 'CodTipoDoc';

    protected $allowedFields = [];

    public function getTipoDocumentoIdentidad()
    {
        try {
            $result = $this->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getTipoDocumentoIdentidadBanco()
    {
        try {
            $result = $this->where('bcp IS NOT NULL OR bbva IS NOT NULL')->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getTipoDocumentoIdentidadByCodTipoDoc($CodTipoDoc, $columnas)
    {
        try {
            $result = $this->select($columnas)->where('CodTipoDoc', $CodTipoDoc)->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
