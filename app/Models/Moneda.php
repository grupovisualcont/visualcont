<?php

namespace App\Models;

use CodeIgniter\Model;

class Moneda extends Model
{
    protected $table            = 'moneda';
    protected $primaryKey       = 'CodMoneda';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'CodMoneda',
        'DescMoneda',
        'AbrevMoneda',
        'Abrev'
    ];

    public function autoCompletado($busqueda)
    {
        $this->select('
            CodMoneda as id,
            DescMoneda as text
        ');
        if (!empty($busqueda)) {
            $this->like('DescMoneda', $busqueda);
        }
        return $this->get()->getResult();
    }
}
