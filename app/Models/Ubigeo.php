<?php

namespace App\Models;

use CodeIgniter\Model;

class Ubigeo extends Model
{
    protected $table = 'ubigeo';

    protected $primaryKey = 'codubigeo';

    protected $allowedFields = [];

    public function getPaises()
    {
        try {
            $result = $this->where('LENGTH(codubigeo) =', 2, FALSE)->orWhere('codubigeo LIKE "9%"')->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
