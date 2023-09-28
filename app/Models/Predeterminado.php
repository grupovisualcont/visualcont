<?php

namespace App\Models;

use CodeIgniter\Model;

class Predeterminado extends Model
{
    protected $table = 'predeterminados';

    protected $returnType     = 'object';

    protected $allowedFields = [
        'IdEstadoSN',
        'CodTipPer_sn',
        'CodTipoDoc_sn',
        'CodUbigeo_sn',
        'IdCondicion_sn',
        'CodMoneda_co',
        'TipoOperacion_co',
        'CodDocumento_co',
        'IdSocioN_co',
        'CodTV_co',
        'CodMoneda_ve',
        'TipoOperacion_ve',
        'CodDocumento_ve',
        'IdSocioN_ve',
        'CodTV_ve',
        'CodTV_cc',
        'CodTV_pp',
        'CodTV_di',
        'CodMoneda_rh',
        'TipoOperacion_rh',
        'CodDocumento_rh',
        'IdSocioN_rh',
        'CodTV_rh',
        'CodTV_ap',
        'CodTV_ac',
        'CodTV_av',
        'CobPagLibre',
        'CodTV_tep',
        'CodMoneda_tep',
        'CodTV_te',
        'CodMoneda_te',
    ];



}
