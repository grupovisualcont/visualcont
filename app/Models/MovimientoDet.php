<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientoDet extends Model
{
    protected $table = 'movimientodet';

    protected $primaryKey = 'IdMovDet';

    protected $allowedFields = [
        'IdMovDet',
        'CodEmpresa',
        'IdMov',
        'Periodo',
        'Mes',
        'NumItem',
        'CodCuenta',
        'DebeSol',
        'HaberSol',
        'DebeDol',
        'HaberDol',
        'CodMoneda',
        'ValorTC',
        'IdSocioN',
        'FecEmision',
        'FecVcto',
        'CodDocumento',
        'SerieDoc',
        'NumeroDoc',
        'NumeroDocF',
        'CodDocumentoRef',
        'SerieDocRef',
        'NumeroDocRef',
        'FecEmisionRef',
        'Destino',
        'RegistroSunat',
        'TipoOperacion',
        'BaseImpSunatS',
        'InafectoS',
        'ExoneradoS',
        'ISCS',
        'IGVSunatS',
        'PercepcionS',
        'OtroTributoS',
        'Retencion4S',
        'TotalS',
        'DescuentoS',
        'AnticipoS',
        'BaseImpSunatD',
        'InafectoD',
        'ExoneradoD',
        'ISCD',
        'IGVSunatD',
        'PercepcionD',
        'OtroTributoD',
        'Retencion4D',
        'TotalD',
        'DescuentoD',
        'AnticipoD',
        'IdPercepcion',
        'DocPercepcion',
        'PorcPercepcion',
        'DocRetencion',
        'PorcRetencion',
        'IdDetraccion',
        'DocDetraccion',
        'PorcDetraccion',
        'FechaDetraccion',
        'CodCcosto',
        'CodCondPago',
        'CodTipoPago',
        'Parametro',
        'Cierre',
        'Conciliado',
        'PeriodoConciliado',
        'MesConciliado',
        'GlosaDet',
        'CodEFE',
        'CodConceptopres',
        'CodBieSer',
        'IdenContProy',
        'CodComprobanteCF',
        'SerieDocCF',
        'NumeroDocCF',
        'codConvenio',
        'codExoneracion',
        'codTipoRenta',
        'codModalidad',
        'Importado',
        'CodTipoCliente',
        'CodVarios',
        'Declarar_Per',
        'Declarar_Est',
        'IdActivo',
        'Monto',
        'Saldo',
        'CtaCte',
        'CodcCostoDet',
        'CodCuentaLibre',
        'CampoLibre1',
        'CampoLibre2',
        'CodTipoSN',
        'TCcierre',
        'TipoPC',
        'IdCheque',
        'NumCheque',
        'IdMovRef_CV',
        'codCuentaDestino',
        'IcbpS',
        'IcbpD',
        'codGrupoCP',
        'IdOperacionAF',
        'IdTipOpeDetra',
        'Validacion',
        'IdMovDetPro',
        'FlagInterno'
    ];
}
