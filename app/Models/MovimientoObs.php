<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientoObs extends Model
{
    protected $table = 'movimientoobs';

    protected $primaryKey = 'IdMovObservacion';

    protected $allowedFields = [
        'CodEmpresa',
        'Origen',
        'Tipo',
        'CodDocumento',
        'Afecto',
        'Inafecto',
        'Exonerado',
        'Igv',
        'Icbp',
        'Descuento',
        'Otro_Tributo',
        'TotalS',
        'TotalD',
        'Caja'
    ];
}
