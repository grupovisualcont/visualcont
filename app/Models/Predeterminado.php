<?php

namespace App\Models;

use CodeIgniter\Model;

class Predeterminado extends Model
{
    protected $table = 'predeterminados';

    protected $allowedFields = [];

    public function getPredeterminado($columnas)
    {
        try {
            $result = $this->select($columnas)->findAll();

            return $result[0];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
