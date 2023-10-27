<?php

namespace App\Models;

use CodeIgniter\Model;

class SidebarDetalles extends Model
{
    protected $table            = 'menudet';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [];

    public function getSidebarDetalles()
    {
        try {
            $result = $this->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
