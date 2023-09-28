<?php

namespace App\Models;

use CodeIgniter\Model;

class CondicionPago extends Model
{
    protected $table = 'condicionpago';

    protected $primaryKey = 'codcondpago';

    protected $allowedFields = [
        'codcondpago',
        'CodEmpresa',
        'desccondpago',
        'comentario',
        'con_cre',
        'Ndias',
        'carga_inicial',
        'Tipo',
        'Estado'
    ];

    public function autoCompletado($busqueda, $codEmpresa)
    {
        $this->select('
            codcondpago as id,
            desccondpago as text
        ');
        if (!empty($busqueda)) {
            $this->like('desccondpago', $busqueda);
        }
        $this->where('codEmpresa', $codEmpresa);
        $this->limit(LIMITE_AUTOCOMPLETADO);
        return $this->get()->getResult();
    }

}
