<?php

namespace App\Models;

use CodeIgniter\Model;

class Ts27Vinculo extends Model
{
    protected $table = 'ts27_vinculo';

    protected $primaryKey = 'CodVinculo';

    protected $allowedFields = [];

    public function getTs27Vinculo()
    {
        try {
            $result = $this->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
