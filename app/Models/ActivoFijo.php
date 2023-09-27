<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivoFijo extends Model
{
    protected $table = 'activosfijos';

    protected $primaryKey = 'IdActivo';

    protected $allowedFields = [
        'codActivo',
        'CodEmpresa',
        'codTipoActivo',
        'descripcion',
        'marca',
        'modelo',
        'serie',
        'fechaAdqui',
        'fechaInicio',
        'fechaRetiro',
        'depresiacion',
        'estado',
        'AnioEstimado',
        'CtaCtableGasto',
        'CtaCtableDepreciacion',
        'CodCcosto',
        'codubigeo',
        'Localidad',
        'Direccion',
        'Responsable',
        'idSituacion',
        'IdCatalogo',
        'IdTipoActivo',
        'IdEstadoActivo',
        'IdMetodo',
        'CodSunatExi',
        'ArrNumero',
        'ArrFecha',
        'ArrCuota',
        'ArrMonto'
    ];
}
