<?php

namespace App\Models;

use CodeIgniter\Model;

class Sidebar extends Model
{
    protected $table            = 'menu';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [];

    public function getSidebar()
    {
        try {
            $result = $this->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
