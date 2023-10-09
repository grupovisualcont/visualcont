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

    public function getMovimientoDet(string $CodEmpresa, int $IdMovDet, int $IdMov, string $columnas, array $join, array $parametros, string $where, string $orderBy)
    {
        try {
            $result = $this;

            if (!empty($columnas)) $result = $this->select($columnas);

            if (is_array($join) && count($join) > 0) {
                foreach ($join as $indice => $valor) {
                    $result = $result->join($valor['tabla'], $valor['on'], $valor['tipo']);
                }
            }

            $result = $result->where('movimientodet.CodEmpresa', $CodEmpresa);

            if (!empty($IdMovDet)) $result = $result->where('movimientodet.IdMovDet', $IdMovDet);

            if (!empty($IdMov)) $result = $result->where('movimientodet.IdMov', $IdMov);

            if (is_array($parametros) && count($parametros) > 0) {
                foreach ($parametros as $indice => $valor) {
                    if (isset($valor['Periodo']) && !empty($valor['Periodo'])) $result = $result->where('movimientodet.Periodo', $valor['Periodo']);

                    if (isset($valor['Mes']) && !empty($valor['Mes'])) $result = $result->where('movimientodet.Mes', $valor['Mes']);

                    if (isset($valor['CodCuenta']) && !empty($valor['CodCuenta'])) $result = $result->where('movimientodet.CodCuenta', $valor['CodCuenta']);

                    if (isset($valor['CodMoneda']) && !empty($valor['CodMoneda'])) $result = $result->where('movimientodet.CodMoneda', $valor['CodMoneda']);

                    if (isset($valor['IdSocioN']) && !empty($valor['IdSocioN'])) $result = $result->where('movimientodet.IdSocioN', $valor['IdSocioN']);

                    if (isset($valor['FecEmision']) && !empty($valor['FecEmision'])) $result = $result->where('DATE(movimientodet.FecEmision)', $valor['FecEmision']);
                    
                    if (isset($valor['CodDocumento']) && !empty($valor['CodDocumento'])) $result = $result->where('movimientodet.CodDocumento', $valor['CodDocumento']);

                    if (isset($valor['SerieDoc']) && !empty($valor['SerieDoc'])) $result = $result->where('movimientodet.SerieDoc', $valor['SerieDoc']);

                    if (isset($valor['NumeroDoc']) && !empty($valor['NumeroDoc'])) $result = $result->where('movimientodet.NumeroDoc', $valor['NumeroDoc']);

                    if (isset($valor['NumeroDocF']) && !empty($valor['NumeroDocF'])) $result = $result->where('movimientodet.NumeroDocF', $valor['NumeroDocF']);

                    if (isset($valor['Parametro']) && !empty($valor['Parametro'])) $result = $result->where('movimientodet.Parametro', $valor['Parametro']);
                }
            }

            if (!empty($where)) $result = $result->where($where);

            if (!empty($orderBy)) $result = $result->orderBy($orderBy);

            $result = $result->asArray()->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
